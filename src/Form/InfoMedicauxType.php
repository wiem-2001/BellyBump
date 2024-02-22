<?php

namespace App\Form;

use App\Entity\InfoMedicaux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class InfoMedicauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maladie')
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
