<?php

namespace App\Form;

use App\Entity\Baby;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class BabyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom')
        ->add('prenom')
        ->add('sexe', ChoiceType::class, [
            'choices' => [
                'Masculin' => 'Masculin',
                'Féminin' => 'Féminin',
            ],
        ])
        ->add('dateNaissance', DateType::class, [
            'label' => 'Date',
            'constraints' => [
                new NotBlank(['message' => 'Entrer une date.']),
                new Callback([$this, 'validateDate']),
            ],
        ])
        ->add('poids')
        ->add('taille');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Baby::class,
        ]);
    }


    public function validateDate($dateNaissance, ExecutionContextInterface $context)
    {
        $currentDay = new \DateTime();
        
        if ($dateNaissance > $currentDay) {
            $context->buildViolation('Vous ne pouvez pas avoir une date dans le futur.')
                ->atPath('dateNaissance')
                ->addViolation();
        }
    }



    
}
