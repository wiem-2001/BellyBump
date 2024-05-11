<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Repository\BabyRepository;
use App\Entity\Baby;
use App\Repository\InfoMedicauxRepository;
use App\Entity\InfoMedicaux;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGeneratiionController extends AbstractController
{

    #[Route('/pdf', name: 'app_generate_pdf', methods: ['GET'])]
    public function generatePdfAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $babies = $entityManager
        ->getRepository(Baby::class)
        ->findAll();


        // Create a Dompdf instance
        $pdfOptions = new Options();
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $dompdf = new Dompdf($pdfOptions);

        // Generate some HTML content for the PDF (replace with your own template)
        $html = $this->renderView('baby/pdf.html.twig', [
            'babies' => $babies, // Pass the data to the PDF template
        ]);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first, load the content into the PDF)
        $dompdf->render();

        // Generate a unique filename for the PDF
        $filename = 'document_' . uniqid() . '.pdf';

        // Save the PDF to a temporary location (you can also store it permanently)
        $output = $dompdf->output();
        file_put_contents($filename, $output);
   // Create a response with the PDF file
   $response = new Response(file_get_contents($filename));
   $response->headers->set('Content-Type', 'application/pdf');
   $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
   $response->headers->set('Cache-Control', 'private');
   $response->headers->set('Pragma', 'private');
   $response->headers->set('Expires', '0');

   // Delete the temporary PDF file
   unlink($filename);

   return $response;
}













#[Route('/pdf2', name: 'app_generate_pdf2', methods: ['GET'])]
    public function generatePdf2(Request $request, EntityManagerInterface $entityManager): Response
    {

        $informations = $entityManager
        ->getRepository(InfoMedicaux::class)
        ->findAll();


        // Create a Dompdf instance
        $pdfOptions = new Options();
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isPhpEnabled', true);
        $dompdf = new Dompdf($pdfOptions);

        // Generate some HTML content for the PDF (replace with your own template)
        $html = $this->renderView('info_medicaux/pdf2.html.twig', [
            'informations' => $informations, // Pass the data to the PDF template
        ]);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (first, load the content into the PDF)
        $dompdf->render();

        // Generate a unique filename for the PDF
        $filename = 'document_' . uniqid() . '.pdf';

        // Save the PDF to a temporary location (you can also store it permanently)
        $output = $dompdf->output();
        file_put_contents($filename, $output);
   // Create a response with the PDF file
   $response = new Response(file_get_contents($filename));
   $response->headers->set('Content-Type', 'application/pdf');
   $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
   $response->headers->set('Cache-Control', 'private');
   $response->headers->set('Pragma', 'private');
   $response->headers->set('Expires', '0');

   // Delete the temporary PDF file
   unlink($filename);

   return $response;
}
}