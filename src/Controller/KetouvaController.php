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

		if ($formKetouva->isSubmitted()) {

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

	#[Route('/ketouva/generate-pdf/{modele}/{id}', name: 'generate_pdf')]
	public function generatePdf($modele, Ketouva $ketouva, PdfGeneratorService $pdfGenerator, CreateKetouva $createKetouva): Response
	{
		$text = $createKetouva->genereTextKetouva($ketouva);
		$text .= ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

		if ($ketouva->getTypeKetouva() == TypeKetouva::NIKREA) {
			$modele .= 'hilouf';
		}

		$pdfContent = $pdfGenerator->generatePdf($text, $modele, $ketouva->getNomFichier(), $ketouva->getTypeKetouva(), $ketouva->getAjustFontSizeInPdf());

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
		$text = str_replace('&nbsp;', ' ', $text);
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

		// Sauvegarder le document
		$tempFile = tempnam(sys_get_temp_dir(), $ketouva->getNomFichier());

		$modeleWord->saveAs($tempFile);

		// Retourner le document comme réponse
		$response = new Response(file_get_contents($tempFile));
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$response->headers->set('Content-Disposition', 'attachment;filename="' . $ketouva->getNomFichier() . '.docx"');

		unlink($tempFile);

		return $response;





		// AUTRE METHODE POUR GENERER UN DOCUMENT WORD

		// Créer une nouvelle instance PHPWord
		$phpWord = new PhpWord();


		// mettre les marges du document à zéro
		// $phpWord->setDefaultFontSize(20);
		// $phpWord->setDefaultFontName('Arial');
		// $phpWord->setDefaultParagraphStyle([
		//     'alignment' => 'left',
		//     'spaceAfter' => 0,
		//     'spaceBefore' => -20
		// ]);


		// Ajouter une police personnalisée
		// $fontPath = $this->getParameter('kernel.project_dir') . '/public/font/ShlomoStam.ttf';
		// $phpWord->addFontStyle('HebrewFont', [
		//     'name' => 'ShlomoStam',
		//     'size' => 12,
		//     'rtl' => true // Activation du mode RTL pour l'hébreu
		// ]);

		// Définir les marges de page à 0
		$sectionStyle = [
			'marginLeft' => 0,
			'marginRight' => 0,
			'marginTop' => 0,
			'marginBottom' => 0,
			'pageSizeW' => Converter::cmToTwip(29.7), // Largeur A3
			'pageSizeH' => Converter::cmToTwip(42.0), // Hauteur A3

			'orientation' => 'portrait'
		];

		// Créer une nouvelle section
		$section = $phpWord->addSection($sectionStyle);

		// Ajouter le PDF en arrière-plan
		$pdfPath = $this->getParameter('kernel.project_dir') . '\public\assets\modele\modele' . $modele . '.png';
		$section->addImage($pdfPath, [
			'positioning' => 'absolute',
			'marginTop' => 0,
			'marginLeft' => 0,
			'width' => Converter::cmToPoint(29.7),    // Largeur A3
			'height' => Converter::cmToPoint(42.0),   // Hauteur A3
			'wrappingStyle' => 'behind',
			'posHorizontal' => 'absolute',
			'posVertical' => 'absolute',
			'posHorizontalRel' => 'page',
			'posVerticalRel' => 'page'
		]);

		// permettre l'écriture en hébreu
		$section->setRtl(true);

		// Enregistrer la police personnalisée
		$fontStyle = [
			'name' => 'ShlomoStam',
			'size' => 18,
			'rtl' => true  // Activation du mode RTL pour l'hébreu
		];
		$phpWord->addFontStyle('hebrewFont', $fontStyle);

		// Créer un textbox avec position et dimensions précises
		// $textbox = $section->addTextBox([
		//     'positioning' => 'absolute',
		//     'posHorizontal' => Converter::cmToPoint(4.54),    // Position X (5cm depuis la gauche)
		//     'posVertical' => Converter::cmToPoint(10.35),     // Position Y (10cm depuis le haut)
		//     'width' => Converter::cmToPoint(20.61),           // Largeur de 15cm
		//     'height' => Converter::cmToPoint(26.67),          // Hauteur de 20cm
		//     'wrappingStyle' => 'square',
		//     'posHorizontalRel' => 'page',
		//     'posVerticalRel' => 'page',
		//     'borderSize' => 0                               // Pas de bordure
		// ]);

		$textbox = $section->addTextBox([
			// 'positioning' => 'absolute',
			'posHorizontal' => 'left',
			'posVertical' => 'top',
			'posHorizontalRel' => 'page', // Changé à 'page' pour un positionnement absolu par rapport à la page
			'posVerticalRel' => 'page',   // Changé à 'page' pour un positionnement absolu par rapport à la page
			'marginLeft' => 128.6929133858, // Position X
			'marginTop' => 2093.3858267717,  // Position Y
			'width' => 584.2204724409,
			'height' => 756,
			'borderSize' => 0,
			'wrappingStyle' => 'square'    // Changé à 'square' pour un meilleur contrôle du positionnement
		]);

		// Ajouter le texte dans le textbox
		$textrun = $textbox->addTextRun(['rtl' => true]);
		$textrun->addText($text, $fontStyle);

		// Sauvegarder le document
		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
		$tempFile = tempnam(sys_get_temp_dir(), 'document');
		$objWriter->save($tempFile);

		// $modeleWord->saveAs($tempFile);

		// Retourner le document comme réponse
		$response = new Response(file_get_contents($tempFile));
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		$response->headers->set('Content-Disposition', 'attachment;filename="document.docx"');

		unlink($tempFile);

		return $response;
	}
}
