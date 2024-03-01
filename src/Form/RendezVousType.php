<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('DateReservation', DateType::class, [
            'label' => 'Date',
            'constraints' => [
                new Assert\Callback([$this, 'validateDate']),
                new Assert\NotBlank(['message' => 'Please enter a date.']),
            ],])

            ->add('heureReservation', IntegerType::class, [
                'constraints' => [
                    new Range([
                        'min' => 8,
                        'max' => 19,
                        'notInRangeMessage' => 'L\'heure de réservation doit être entre {{ min }} heures et {{ max }} heures.',
                    ]),
                    new Assert\NotBlank(['message' => 'Please enter the houre']),
                ],
                
            ])
            ->add('nomMed', EntityType::class, [
                'class' => \App\Entity\Med::class,
                'choice_label' => 'nom', // Choisissez le champ à afficher dans la liste déroulante
                'placeholder' => 'Choisissez un médecin', // Texte facultatif pour l'option par défaut
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please select doctor ']),
                ],
            ])
        ;
    
    }
    
    public function validateDate($date, ExecutionContextInterface $context){
        $currentDay = new \DateTime();
        
        if($date < $currentDay){
            $context->buildViolation('This date is in the pass please try again')
            ->atPath('date')->addViolation();
        }
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
