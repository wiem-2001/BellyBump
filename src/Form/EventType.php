<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Name',
                'mapped' => True,
                'constraints'=> [new Assert\NotBlank(array("message" => "Please enter a name")),]
            ])
            ->add('description',TextareaType::class,
            ['label'=>'Description', 
            'mapped' => True,
            'constraints'=> [new Assert\NotBlank(array("message" => "Please enter a description of the event")),]

            ])
            ->add('MeetingCode', TextType::class,['label'=>'Meeting Code',
            'constraints'=> [new Assert\NotBlank(array("message" => "Please enter a meeting code ")),]
            ])
            ->add('coach',EntityType::class,[
                'class'=>'App\Entity\Coach',
                'placeholder'=>'select coach',
            ])
            ->add('day',DateType::class,['label'=>'Day','constraints'=>[ new Callback([$this,'validateDate'])]])
            ->add('heureDebut',TimeType::class,['label'=>'Start Time'])
            ->add('heureFin',TimeType::class,['label'=>'End Time', 'constraints'=>[ new Callback([$this,'validateHeureFin'])]])
            ->add('image',FileType::class,[
                'label' => 'Choose Image',
                'data_class'=> null ,
                'constraints'=> [new Assert\NotBlank(array("message" => "Please choose an image")),]

            ])
            
        ;
    }

    public function validateHeureFin($heureFin, ExecutionContextInterface $context)
    {
        $form = $context->getRoot();
        $heureDebut = $form->get('heureDebut')->getData();

        if ($heureDebut >= $heureFin) {
            $context->buildViolation('The end time must be after the start time.')
                ->atPath('heureFin')
                ->addViolation();
        }
    }

    public function validateDate($date, ExecutionContextInterface $context){
        $currentDay = new \DateTime();
        
        if($date < $currentDay){
            $context->buildViolation('You can\'t create an event in the past!')
            ->atPath('day')->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
