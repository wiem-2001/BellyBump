<?php
//src/Services/PdfGenerator

namespace App\Service;

use Dompdf\Dompdf;
use App\Entity\Baby;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfGeneratorBaby
{
    private $entityManager;
    private $twig;

    public function __construct(EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function generateBabyListPdf(): Response
    {
        // Fetch list of babies from the database
        $babyRepository = $this->entityManager->getRepository(Baby::class);
        $babies = $babyRepository->findAll();

        // Render the Twig template with the list of babies
        $html = $this->twig->render('baby/baby_list_pdf.html.twig', [
            'babies' => $babies,
        ]);

        // Generate the PDF using domPDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Return the PDF as a Symfony Response
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="baby_list.pdf"');

        return $response;
    }
}
