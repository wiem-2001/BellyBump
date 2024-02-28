<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/sendmail/{id}', name: 'mailing',methods: ['GET'])]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('kharrat.raed@esprit.tn')
            ->to('wiem.ayari@esprit.tn')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Dossier medical')
            ->text('Sending emails is fun again!')
            ->html('<p>hello Doctor 😑  </p> <p> this mail is send automaticly send by the mother for sharing my baby medical information  </p><p>if you think its dangerous or m baby is sick ,mail me back and thanks');
    
        $mailer->send($email);
    
        // Return a response, for example, a simple acknowledgment message.
        return new Response('Email sent successfully');
    }
}
