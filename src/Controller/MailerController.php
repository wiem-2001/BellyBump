<?php

namespace App\Controller;

use App\Entity\InfoMedicaux;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/sendmail/{id}', name: 'mailing',methods: ['GET'])]
    public function sendEmail(MailerInterface $mailer, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $infoMedicaux = $entityManager->getRepository(InfoMedicaux::class)->find($id);

        // Vérifier si le bébé existe
        if (!$infoMedicaux) {
            throw $this->createNotFoundException('Info Medicaux not found for id '.$id);
        }

        // Récupérer les informations médicales du bébé

        // Construire le contenu de l'e-mail avec les informations médicales
        $emailContent = "Bonjour Docteur,\n\n";
        $emailContent .= "Voici un résumé des informations médicales :\n\n";
        $emailContent .= "Maladie: ".$infoMedicaux->getMaladie()."\n";
        $emailContent .= "Description: ".$infoMedicaux->getDescription()."\n";
        $emailContent .= "Nombre de vaccins: ".$infoMedicaux->getNbrVaccin()."\n";
        $emailContent .= "Date du dernier vaccin: ".$infoMedicaux->getDateVaccin()->format('Y-m-d')."\n";
        $emailContent .= "Groupe sanguin: ".$infoMedicaux->getBloodType()."\n";
        $emailContent .= "Estimation de la maladie: ".$infoMedicaux->getSicknessEstimation()."\n\n";
        $emailContent .= "Si vous avez des questions ou des préoccupations, n'hésitez pas à me contacter.\n\n";
        $emailContent .= "Cordialement,\n";
       
        
        

        // Créer l'objet Email
        $email = (new Email())
            ->from('kharrat.raed@esprit.tn')
            ->to('wiem.ayari@esprit.tn')
            ->subject('Dossier médical')
            ->text($emailContent);

        // Envoyer l'e-mail
        $mailer->send($email);

        // Retourner une réponse
        return new Response('Email sent successfully');
    }
}
