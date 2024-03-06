<?php

namespace App\Form;

use App\Entity\Med;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom',TextType::class,[
            'label' => 'Nom',
            'mapped' => True,
            'constraints'=> [new Assert\NotBlank(array("message" => "Please enter a name")),]
        ])
        ->add('prenom',TextType::class,[
            'label' => 'Prenom',
            'mapped' => True,
            'constraints'=> [new Assert\NotBlank(array("message" => "Please enter a Last Name")),]
        ])
        ->add('specialite',TextType::class,[
            'label' => 'Specialite',
            'mapped' => True,
            'constraints'=> [new Assert\NotBlank(array("message" => "Please enter a Speciality")),]
        ])
            ->add('etab', EntityType::class, [
                'class' => \App\Entity\Etab::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choose an establishment',
                'constraints'=> [new Assert\NotBlank(array("message" => "Please enter an etablishment")),]
                ])
            ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Med::class,
        ]);
    }
    
}
