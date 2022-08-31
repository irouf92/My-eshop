<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label'=> 'Titre produit',
            ])
            ->add( 'description')
            ->add('color', TextType::class,[
                'label'=> 'Couleur',
            ])
            ->add('size', ChoiceType::class,[
                'label'=> 'Taille',
                'choices'=>[
                    'S'=> 's',
                    'M'=>'m',
                    'L'=>'l',
                    'XL'=>'xl'
                ],
            ])
            ->add( 'gender', ChoiceType::class, [
            'label' => 'Genre',
            'choices'=>[
                'Femme'=>'femme',
                'Homme'=>'homme'
            ]
        ])
            ->add('photo', FileType::class, [
                'label' => 'Photo du produit',

            ])
            ->add( 'price', TextType::class, [
            'label' => 'Prix unitaire',
        ])
            ->add('stock')
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter produit',
                'attr' => [
                    'class' => 'd-block mx-auto btn btn-warning col-4'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'allow_file_upload'=> true,
            'photo'=> null,
        ]);
    }
}