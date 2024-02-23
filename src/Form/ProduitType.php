<?php
namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Partenaire;

class ProduitType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('nom', TextType::class)
->add('description', TextareaType::class)
->add('prix', MoneyType::class)
    ->add('partenaire', EntityType::class, [
        'class' => Partenaire::class,
        'choice_label' => 'marque', // ou un autre attribut de Partenaire que vous souhaitez afficher
        'label' => 'brandPartner',])
->add('imageFile', VichFileType::class, [
'required' => false,
'allow_delete' => true, // permet la suppression de l'image
'download_uri' => true, // permet le téléchargement de l'image
'label' => 'Image (fichier JPG ou PNG)'
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Produit::class,
]);
}
}
