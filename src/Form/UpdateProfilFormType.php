<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('adress', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Phone Number (Tunisia)',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a phone number',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'Please enter a valid 8-digit phone number for Tunisia (e.g., 12345678)',
                    ]),
                ],
            ])
            ->add('image',FileType::class,[
                'label' => 'Choose Image',
                'data_class'=> null ,
                'constraints'=> [new Assert\NotBlank(array("message" => "Please choose an image")),]

            ])
            ->add('birthday',BirthdayType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
