<?php

namespace App\Controller;

use App\Constant\ModeleKetouva;
use App\Constant\StatutKala;
use App\Constant\TypeKetouva;
use App\Entity\Ketouva;
use App\Form\KetouvaFormType;
use App\Repository\KetouvaRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\PdfGeneratorService;
use App\Services\CreateKetouva;
use DateTime;
use DateTimeImmutable;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpParser\Node\Expr\AssignOp\Mod;

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

		if ($type == TypeKetouva::BETOULA) {
			$ketouva->setStatutKala(StatutKala::BETOULA['hebreu']);
		}

		if ($type == TypeKetouva::BETOULA || $type == TypeKetouva::CINQUANTE) {
			$ketouva->setEcartLigne(8.5);
		} else {
			$ketouva->setEcartLigne(7);
		}

		$formKetouva = $this->createForm(KetouvaFormType::class, $ketouva, [
			'type' => $type
		]);

		$formKetouva->handleRequest($request);

		if ($formKetouva->isSubmitted()) {

			if (!$ketouva->getNomFichier()) {
				$ketouva->setNomFichier($type . '-' . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
			}

			$em->persist($ketouva);
			$em->flush();

			// $this->addFlash('success', 'Ketouva créée avec succès');

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

		if ($formKetouva->isSubmitted()) {

			if (!$ketouva->getNomFichier()) {
				$ketouva->setNomFichier($type . '-' . date('d') . '-' . date('m') . '-' . date('Y') . '-' . date('H') . '_' . date('i') . '_' . date('s'));
			}

			$ketouva->setEditedAt(new DateTime());

			$em->flush();

			// $this->addFlash('success', 'Ketouva modifiée avec succès');

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

		$textKetouvaHtml = $createKetouva->genereTextKetouvaHtml($ketouva);

		return $this->render('ketouva/rendu.html.twig', [
			'ketouva' => $ketouva,
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

	#[Route('/ketouva/generate-pdf/{modele}/{id}', name: 'generate_pdf')]
	public function generatePdf($modele, Ketouva $ketouva, PdfGeneratorService $pdfGenerator, CreateKetouva $createKetouva): Response
	{
		$text = $createKetouva->genereTextKetouva($ketouva);

		if ($ketouva->getTypeKetouva() == TypeKetouva::NIKREA) {
			$modele .= 'hilouf';
		}

		$pdfContent = $pdfGenerator->generatePdf($text, $modele, $ketouva);

		$response = new Response($pdfContent);
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'attachment; filename="' . $ketouva->getNomFichier() . '.pdf"');

		return $response;
	}

	#[Route('/ketouva/generate-word/{modele}/{id}', name: 'generate_word')]
	public function generateWord($modele, Ketouva $ketouva, CreateKetouva $createKetouva): Response
	{
		if ($ketouva->getTypeKetouva() == TypeKetouva::NIKREA) {
			$modele .= 'hilouf';
		}

		$text = $createKetouva->genereTextKetouva($ketouva);

		$text = str_replace('<br>', ' ', $text);
		$text = str_replace('<span>', '', $text);
		$text = str_replace('</span>', '', $text);

		$neoum = ModeleKetouva::NEOUM . '
        ' . ModeleKetouva::NEOUM;

		$tailleModele =  'grand';
		$fin = '';
		if ($ketouva->getTypeKetouva() == TypeKetouva::TAOUTA || $ketouva->getTypeKetouva() == TypeKetouva::IRKESSA || $ketouva->getTypeKetouva() == TypeKetouva::NIKREA) {
			$fin = ModeleKetouva::FIN_REECRITURE;
			$tailleModele = $ketouva->getTypeKetouva() != TypeKetouva::NIKREA ? 'petit' : '';
		}


		$modeleWord = new TemplateProcessor($this->getParameter('kernel.project_dir') . '/public/assets/modele/modele' . $modele . $tailleModele . '.docx');
		$modeleWord->setValue('text', $text);
		$modeleWord->setValue('neoum', $neoum);
		$modeleWord->setValue('fin', $fin);

		$texteVerso = CreateKetouva::getTexteVersoWord($ketouva);
		$modeleWord->setValue('texteVerso', $texteVerso);

		// Sauvegarder le document
		$tempFile = tempnam(sys_get_temp_dir(), $ketouva->getNomFichier());

		$modeleWord->saveAs($tempFile);

		// Retourner le document comme réponse
		$response = new Response(file_get_contents($tempFile));
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$response->headers->set('Content-Disposition', 'attachment;filename="' . $ketouva->getNomFichier() . '.docx"');

		unlink($tempFile);

		return $response;
	}
}
