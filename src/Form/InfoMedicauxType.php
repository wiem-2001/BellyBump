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
            ->add('dateVaccin')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InfoMedicaux::class,
        ]);
    }
}
