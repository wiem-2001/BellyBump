<?php

namespace App\Form;

use App\Entity\Coach;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class CoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter coach first name.']),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter coach last name.']),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\Email([
                        'message' => 'The email "{{ value }}" is not a valid email.',
                    ]),
                    new Assert\NotBlank([
                        'message' => 'Please enter an email address.',
                    ]),
                ],
            ])
            ->add('job', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter coach speciality Job .']),
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone Number ',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a phone number',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'Please enter a valid 8-digit phone number for Tunisia ',
                    ]),
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coach::class,
        ]);
    }
}
