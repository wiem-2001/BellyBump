<?php

namespace App\Form;

use App\Entity\User;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'mapped' => True,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your first name.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'mapped' => True,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter your last name.',
                    ]),
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
            ->add('adress', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter your address.',]),
                ],
            ])
            ->add('phoneNumber', NumberType::class, [
                'label' => 'Phone Number (Tunisia)',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter 8-digit phone number',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'Please enter a valid 8-digit phone number for Tunisia (e.g., 12345678)',
                    ]),
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'password must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Password Confirmation'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' =>  8,
                        'minMessage' => 'Your password must be at least {{ limit }} characters long ',
                        'max' =>  20,
                        'maxMessage' => 'Your password cannot be longer than {{ limit }} characters.',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                        'message' => 'Your password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
                    ]),
                ],
            ])
            ->add('image',FileType::class,[
                'label' => 'Choose Image',
                'data_class'=> null ,
                'constraints'=> [new Assert\NotBlank(array("message" => "Please choose an image")),]

            ])
            ->add('birthday', BirthdayType::class)
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
