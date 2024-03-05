<?php

namespace App\Form;

use App\Entity\InfoMedicaux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Baby;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;





class InfoMedicauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('maladie')
            ->add('BloodType', ChoiceType::class, [
                'choices' => [
                    'A+' => 'A+',
                    'A-' => 'A-',
                    'B+' => 'B+',
                    'B-' => 'B-',
                    'AB+' => 'AB+',
                    'AB-' => 'AB-',
                    'O+' => 'O+',
                    'O-' => 'O-',
                ],
            ])
            ->add('sicknessEstimation', ChoiceType::class, [
                'choices' => [
                    '25%' => '25%',
                    '50%' => '50%',
                    '75%' => '75%',
                    '100%' => '100%',
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 3], // Set the number of rows here
            ])
            ->add('nbrVaccin')
            ->add('dateVaccin', DateType::class, [
            'label' => 'Date',
            'constraints' => [
                new NotBlank(['message' => 'Entrer une date.']),
                new Callback([$this, 'validateDate']),
            ],
            ])
            ->add('Med', EntityType::class, [
                'class' => \App\Entity\Med::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choose an establishment',
                'constraints'=> [new Assert\NotBlank(array("message" => "Please enter an etablishment")),]
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoMedicaux::class,
        ]);
    }


    public function validateDate($dateVaccin, ExecutionContextInterface $context)
    {
        $currentDay = new \DateTime();
        
        if ($dateVaccin < $currentDay) {
            $context->buildViolation('Vous ne pouvez pas avoir une date dans le passe.')
                ->atPath('dateVaccin')
                ->addViolation();
        }
    }
}
