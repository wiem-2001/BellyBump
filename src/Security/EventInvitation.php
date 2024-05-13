<?php

namespace App\Security;

use App\Entity\Event;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class EventInvitation
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function sendMeetingInvite(Event $Event ,TemplatedEmail $meetingEmail): void
    {
        $context= $meetingEmail->getContext();
        $context[ 'Event' ] =$Event ;
        $meetingEmail->context( $context);
    
        $this->mailer->send($meetingEmail);
    }
}
