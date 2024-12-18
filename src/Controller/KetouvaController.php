<?php

namespace App\Controller;

use App\Constant\StatutKala;
use App\Entity\Ketouva;
use App\Form\KetouvaFormType;
use App\Repository\KetouvaRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeKetouvaRepository;
use App\Services\CalculeMois;
use App\Services\CalculeProvenanceKala;
use App\Services\CreateKetouva;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class KetouvaController extends AbstractController
{
    #[Route('/create/ketouva/{type}', name: 'create_ketouva', methods: ['GET', 'POST'])]
    public function createKEtouva(
        $type,
        Request $request,
        // CalculeMois $calculeMois,
        // CalculeProvenanceKala $calculeProvenanceKala,
        // CreateKetouva $createKetouva,
        EntityManagerInterface $em,
        // TypeKetouvaRepository $typeKetouvaRepository
    ): Response {
        $ketouva = new Ketouva;

        $ketouva->setTypeKetouva($type);

        if ($type == 'habad' || $type == 'sefarad') {
            $ketouva->setStatutKala(StatutKala::BETOULA['hebreu']);
        }

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva, [
            'type' => $type
        ]);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            //     $mois = $calculeMois->getMoisKetouva($ketouva);
            //     $provenanceKala = $calculeProvenanceKala->getProvenanceKala($ketouva);

            //     $modele = $createKetouva->genereKetouva($ketouva, $mois, $provenanceKala, 'habad');

            //     if (!$ketouva->getNomFichier()) {
            //         $ketouva->setNomFichier("habad-" . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            //     }

            //     $modele->saveAs('ketouvot/' . $ketouva->getNomFichier() . '.docx');

            //     $ketouva->setCreatedAt(new DateTime());

            //     if ($ketouva->getId() == null) {
            //         $em->persist($ketouva);
            //     }
            //     $em->flush();

            //     return $this->render('ketouva/rendu/habad-sefarad/index.html.twig', [
            //         'ketouva' => $ketouva,
            //         'mois' => $mois,
            //         'provenanceKala' => $provenanceKala,
            //         'habadSefarad' => 'מדאוריתא',
            //         'lienActif' => 'habad',
            //         'type' => 'habad'
            //     ]);
        }

        return $this->render('ketouva/form.html.twig', [
            'form' => $formKetouva->createView(),
            'typeKetouva' => $type,
            'lienActif' => $type
        ]);
    }

    /**
     * @Route("/ketouva/habad/{id}/edit", name="ketouva_habad_edit")
     */
    public function edit_habad(
        $id,
        Request $request,
        CalculeMois $calculeMois,
        CalculeProvenanceKala $calculeProvenanceKala,
        CreateKetouva $createKetouva,
        EntityManagerInterface $em,
        KetouvaRepository $ketouvaRepository
    ): Response {

        $ketouva = $ketouvaRepository->findOneBy(['id' => $id]);

        if ($ketouva == null or $ketouva->getTypeKetouva()->getNomType() != 'habad') {
            return $this->redirectToRoute('homepage');
        }

        // $ketouva->setNomFichier(str_replace('habad-', '', $ketouva->getNomFichier()));

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            $mois = $calculeMois->getMoisKetouva($ketouva);
            $provenanceKala = $calculeProvenanceKala->getProvenanceKala($ketouva);

            $modele = $createKetouva->genereKetouva($ketouva, $mois, $provenanceKala, 'habad');

            if (!$ketouva->getNomFichier()) {
                $ketouva->setNomFichier("habad-" . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            }

            $modele->saveAs('ketouvot/' . $ketouva->getNomFichier() . '.docx');

            if (!isset($_POST['idKetouva'])) {
                $ketouva->setModifiedAt(new DateTime());
            }

            if ($ketouva->getId() == null) {
                $em->persist($ketouva);
            }
            $em->flush();

            return $this->render('ketouva/rendu/habad-sefarad/index.html.twig', [
                'ketouva' => $ketouva,
                'mois' => $mois,
                'provenanceKala' => $provenanceKala,
                'habadSefarad' => 'מדאוריתא',
                'lienActif' => 'habad',
                'type' => 'habad'
            ]);
        }

        return $this->render('ketouva/form/habad-sefarad.html.twig', [
            'formKetouva' => $formKetouva->createView(),
            'typeKetouva' => 'Habad',
            'lienActif' => 'habad'
        ]);
    }

    /**
     * @Route("/ketouva/sefarad", name="ketouva_sefarad")
     */
    public function sefarad(
        Request $request,
        CalculeMois $calculeMois,
        CalculeProvenanceKala $calculeProvenanceKala,
        CreateKetouva $createKetouva,
        EntityManagerInterface $em,
        TypeKetouvaRepository $typeKetouvaRepository
    ): Response {
        $ketouva = new Ketouva;

        // faire ça maintenant pour que le formulaire le prenne en compte
        $typeSefarad = $typeKetouvaRepository->findOneBy(['nomType' => 'sefarad']);
        $ketouva->setTypeKetouva($typeSefarad);

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            $mois = $calculeMois->getMoisKetouva($ketouva);
            $provenanceKala = $calculeProvenanceKala->getProvenanceKala($ketouva);

            $modele = $createKetouva->genereKetouva($ketouva, $mois, $provenanceKala, 'sefarad');

            if (!$ketouva->getNomFichier()) {
                $ketouva->setNomFichier("sefarad-" . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            }

            $modele->saveAs('ketouvot/' . $ketouva->getNomFichier() . '.docx');

            $ketouva->setCreatedAt(new DateTime());

            if ($ketouva->getId() == null) {
                $em->persist($ketouva);
            }
            $em->flush();


            return $this->render('ketouva/rendu/habad-sefarad/index.html.twig', [
                'ketouva' => $ketouva,
                'mois' => $mois,
                'provenanceKala' => $provenanceKala,
                'habadSefarad' => '',
                'lienActif' => 'sefarad',
                'type' => 'sefarad'
            ]);
        }

        return $this->render('ketouva/form/habad-sefarad.html.twig', [
            'formKetouva' => $formKetouva->createView(),
            'typeKetouva' => 'Sefarad',
            'lienActif' => 'sefarad'
        ]);
    }

    /**
     * @Route("/ketouva/sefarad/{id}/edit", name="ketouva_sefarad_edit")
     */
    public function edit_sefarad(
        $id,
        Request $request,
        CalculeMois $calculeMois,
        CalculeProvenanceKala $calculeProvenanceKala,
        CreateKetouva $createKetouva,
        EntityManagerInterface $em,
        KetouvaRepository $ketouvaRepository
    ): Response {

        $ketouva = $ketouvaRepository->findOneBy(['id' => $id]);

        if ($ketouva == null or $ketouva->getTypeKetouva()->getNomType() != 'sefarad') {
            return $this->redirectToRoute('homepage');
        }

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            $mois = $calculeMois->getMoisKetouva($ketouva);
            $provenanceKala = $calculeProvenanceKala->getProvenanceKala($ketouva);

            $modele = $createKetouva->genereKetouva($ketouva, $mois, $provenanceKala, 'sefarad');

            if (!$ketouva->getNomFichier()) {
                $ketouva->setNomFichier("sefarad-" . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            }

            $modele->saveAs('ketouvot/' . $ketouva->getNomFichier() . '.docx');

            if (!isset($_POST['idKetouva'])) {
                $ketouva->setModifiedAt(new DateTime());
            }

            if ($ketouva->getId() == null) {
                $em->persist($ketouva);
            }
            $em->flush();


            return $this->render('ketouva/rendu/habad-sefarad/index.html.twig', [
                'ketouva' => $ketouva,
                'mois' => $mois,
                'provenanceKala' => $provenanceKala,
                'habadSefarad' => '',
                'lienActif' => 'sefarad',
                'type' => 'sefarad'
            ]);
        }

        return $this->render('ketouva/form/habad-sefarad.html.twig', [
            'formKetouva' => $formKetouva->createView(),
            'typeKetouva' => 'Sefarad',
            'lienActif' => 'Sefarad'
        ]);
    }

    /**
     * @Route("/ketouva/5050", name="ketouva_5050")
     */
    public function ketouva_5050(
        Request $request,
        CalculeMois $calculeMois,
        CalculeProvenanceKala $calculeProvenanceKala,
        CreateKetouva $createKetouva,
        EntityManagerInterface $em,
        TypeKetouvaRepository $typeKetouvaRepository
    ): Response {
        $ketouva = new Ketouva;

        // faire ça maintenant pour que le formulaire le prenne en compte
        $type5050 = $typeKetouvaRepository->findOneBy(['nomType' => '5050']);
        $ketouva->setTypeKetouva($type5050);

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            $mois = $calculeMois->getMoisKetouva($ketouva);
            $provenanceKala = $calculeProvenanceKala->getProvenanceKala($ketouva);

            $modele = $createKetouva->genereKetouva($ketouva, $mois, $provenanceKala, '5050');

            if (!$ketouva->getNomFichier()) {
                $ketouva->setNomFichier("5050-" . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            }

            $modele->saveAs('ketouvot/' . $ketouva->getNomFichier() . '.docx');

            $ketouva->setCreatedAt(new DateTime());

            if ($ketouva->getId() == null) {
                $em->persist($ketouva);
            }
            $em->flush();

            return $this->render('ketouva/rendu/5050/index.html.twig', [
                'ketouva' => $ketouva,
                'mois' => $mois,
                'lienActif' => '5050'
            ]);
        }

        return $this->render('ketouva/form/5050.html.twig', [
            'formKetouva' => $formKetouva->createView(),
            'lienActif' => '5050'
        ]);
    }

    /**
     * @Route("/ketouva/5050/{id}/edit", name="ketouva_5050_edit")
     */
    public function edit_ketouva_5050(
        $id,
        Request $request,
        CalculeMois $calculeMois,
        CalculeProvenanceKala $calculeProvenanceKala,
        CreateKetouva $createKetouva,
        EntityManagerInterface $em,
        KetouvaRepository $ketouvaRepository
    ): Response {

        $ketouva = $ketouvaRepository->findOneBy(['id' => $id]);

        if ($ketouva == null or $ketouva->getTypeKetouva()->getNomType() != '5050') {
            return $this->redirectToRoute('homepage');
        }

        $formKetouva = $this->createForm(KetouvaFormType::class, $ketouva);

        $formKetouva->handleRequest($request);

        if ($formKetouva->isSubmitted() && $formKetouva->isValid()) {

            $mois = $calculeMois->getMoisKetouva($ketouva);
            $provenanceKala = $calculeProvenanceKala->getProvenanceKala($ketouva);

            $modele = $createKetouva->genereKetouva($ketouva, $mois, $provenanceKala, '5050');

            if (!$ketouva->getNomFichier()) {
                $ketouva->setNomFichier("5050-" . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
            }

            $modele->saveAs('ketouvot/' . $ketouva->getNomFichier() . '.docx');

            if (!isset($_POST['idKetouva'])) {
                $ketouva->setModifiedAt(new DateTime());
            }

            if ($ketouva->getId() == null) {
                $em->persist($ketouva);
            }
            $em->flush();

            return $this->render('ketouva/rendu/5050/index.html.twig', [
                'ketouva' => $ketouva,
                'mois' => $mois,
                'lienActif' => '5050'
            ]);
        }

        return $this->render('ketouva/form/5050.html.twig', [
            'formKetouva' => $formKetouva->createView(),
            'lienActif' => '5050'
        ]);
    }


    #[Route('/ketouva/liste', name: 'ketouva_list')]
    public function list_ketouva(KetouvaRepository $ketouvaRepository)
    {
        $ketouvot = $ketouvaRepository->findBy([], ['id' => 'desc']);

        return $this->render('ketouva/list.html.twig', [
            'ketouvot' => $ketouvot,
            'lienActif' => 'listKetouva'
        ]);
    }

    /**
     * @Route("/ketouva/supprimer-ketouva/{id}", name="supprimer_ketouva")
     */
    public function supprimerKetouva($id, KetouvaRepository $ketouvaRepository, EntityManagerInterface $em)
    {
        // vérifier que l'id existe bien
        $ketouva = $ketouvaRepository->findOneBy(['id' => $id]);

        if ($ketouva != null) {
            // supprimer la ligne
            $em->remove($ketouva);
            $em->flush();
            $this->addFlash('danger', 'La ketouva ' . $ketouva->getTypeKetouva()->getNomType() . ' "' . $ketouva->getNomFichier() . '" a bien été supprimée');
        }

        if (file_exists('ketouvot/' . $ketouva->getNomFichier() . '.docx')) {
            unlink('ketouvot/' . $ketouva->getNomFichier() . '.docx');
        }

        return $this->redirectToRoute('ketouva_list');
    }
}
