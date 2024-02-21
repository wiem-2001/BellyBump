<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter a title.'])
                ]
            ])
            ->add('auteur', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter an author name.'])
                ]
            ])
            ->add('content', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter content.'])
                ]
            ])
            ->add('createdat')
            ->add('image',FileType::class,[
                'label' => 'Choose Image',
                'data_class'=> null ,
                'constraints'=> [new Assert\NotBlank(array("message" => "Please choose an image")),]

            ])
            ->add('ajouter',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
