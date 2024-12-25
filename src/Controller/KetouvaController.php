<?php

namespace App\Controller;

use App\Constant\StatutKala;
use App\Constant\TypeKetouva;
use App\Entity\Ketouva;
use App\Form\KetouvaFormType;
use App\Repository\KetouvaRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeKetouvaRepository;
use App\Services\CalculeMois;
use App\Services\CalculeProvenanceKala;
use App\Services\CreateKetouva;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KetouvaController extends AbstractController
{
    #[Route('/create/ketouva/{type}', name: 'create_ketouva', methods: ['GET', 'POST'])]
    public function createKEtouva($type, Request $request, EntityManagerInterface $em): Response
    {
        if (!in_array($type, (new \ReflectionClass(TypeKetouva::class))->getConstants(), true)) {
            throw new NotFoundHttpException('Invalid ketouva type.');
        }

        $ketouva = new Ketouva;

        $ketouva->setTypeKetouva($type);

        if ($type == TypeKetouva::HABAD || $type == TypeKetouva::SEFARAD) {
            $ketouva->setStatutKala(StatutKala::BETOULA['hebreu']);
        }

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva, [
            'type' => $type
        ]);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            if (!$ketouva->getNomFichier()) {
                $ketouva->setNomFichier($type . '-' . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            }

            $em->persist($ketouva);
            $em->flush();

            $this->addFlash('success', 'Ketouva créée avec succès');

            return $this->redirectToRoute('affiche_ketouva', [
                'id' => $ketouva->getId()
            ]);
        }

        return $this->render('ketouva/form.html.twig', [
            'ketouva' => $ketouva,
            'form' => $formKetouva->createView(),
            'typeKetouva' => $type,
            'lienActif' => $type
        ]);
    }

    #[Route('/edit/ketouva/{id}', name: 'edit_ketouva', methods: ['GET', 'POST'])]
    public function editKEtouva(Ketouva $ketouva, Request $request, EntityManagerInterface $em): Response
    {
        $type = $ketouva->getTypeKetouva();

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva, [
            'type' => $type
        ]);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            if (!$ketouva->getNomFichier()) {
                $ketouva->setNomFichier($type . '-' . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            }

            $ketouva->setEditedAt(new DateTime());

            $em->flush();

            $this->addFlash('success', 'Ketouva modifiée avec succès');

            return $this->redirectToRoute('affiche_ketouva', [
                'id' => $ketouva->getId()
            ]);
        }

        return $this->render('ketouva/form.html.twig', [
            'ketouva' => $ketouva,
            'form' => $formKetouva->createView(),
            'typeKetouva' => $type,
            'lienActif' => $type
        ]);
    }

    #[Route('/show/ketouva/{id}', name: 'affiche_ketouva', methods: ['GET', 'POST'])]
    public function afficheKetouva(Ketouva $ketouva, CreateKetouva $createKetouva,): Response
    {
        $type = $ketouva->getTypeKetouva();

        $textKetouva = $createKetouva->genereTextKetouva($ketouva);
        $textKetouvaHtml = $createKetouva->genereTextKetouvaHtml($ketouva);

        return $this->render('ketouva/rendu.html.twig', [
            'ketouva' => $ketouva,
            'textKetouva' => $textKetouva,
            'textKetouvaHtml' => $textKetouvaHtml,
            'lienActif' => $type,
            'type' => $type
        ]);
    }

    #[Route('/ketouva/list', name: 'ketouva_list')]
    public function listKetouva(KetouvaRepository $ketouvaRepository)
    {
        $ketouvot = $ketouvaRepository->findBy(['deletedAt' => null], ['id' => 'desc']);

        return $this->render('ketouva/list.html.twig', [
            'ketouvot' => $ketouvot,
            'lienActif' => 'listKetouva'
        ]);
    }

    #[Route('/delete/ketouva/{id}', name: 'supprimer_ketouva')]
    public function supprimerKetouva(Ketouva $ketouva, EntityManagerInterface $em): Response
    {
        $ketouva->setDeletedAt(new DateTimeImmutable());

        $em->flush();

        $this->addFlash('danger', 'Ketouva supprimée avec succès.');

        return $this->redirectToRoute('ketouva_list');
    }
}
