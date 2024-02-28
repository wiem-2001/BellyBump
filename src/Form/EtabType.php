<?php

namespace App\Form;

use App\Entity\Etab;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EtabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom',TextType::class,[
            'label' => 'Nom',
            'mapped' => True,
            'constraints'=> [new Assert\NotBlank(array("message" => "Please enter a name")),]
        ])
        ->add('Localisation', ChoiceType::class, [
            'choices' => [
                'Ariana' => 'ariana',
                'Béja' => 'beja',
                'Ben Arous' => 'ben_arous',
                'Bizerte' => 'bizerte',
                'Gabès' => 'gabes',
                'Gafsa' => 'gafsa',
                'Jendouba' => 'jendouba',
                'Kairouan' => 'kairouan',
                'Kasserine' => 'kasserine',
                'Kébili' => 'kebili',
                'Le Kef' => 'le_kef',
                'Mahdia' => 'mahdia',
                'Manouba' => 'manouba',
                'Médenine' => 'medenine',
                'Monastir' => 'monastir',
                'Nabeul' => 'nabeul',
                'Sfax' => 'sfax',
                'Sidi Bouzid' => 'sidi_bouzid',
                'Siliana' => 'siliana',
                'Sousse' => 'sousse',
                'Tataouine' => 'tataouine',
                'Tozeur' => 'tozeur',
                'Tunis' => 'tunis',
                'Zaghouan' => 'zaghouan',
            ],
            'placeholder' => 'Sélectionnez adresse',
            'required' => true,
        ])
        
        ->add('type', ChoiceType::class, [
            'choices' => [
                'Public' => 'public',
                'Privé' => 'prive',
            ],
            'placeholder' => 'Sélectionnez le type',
            'required' => true,
        ])
            ->add('Med', CollectionType::class, [
                'entry_type' => MedType::class,
                'by_reference' => false,
                'allow_add' => true,
                'mapped' => false, // Mettez cette ligne à false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etab::class,
        ]);
    }
}
