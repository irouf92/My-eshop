<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide'
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 255,
                        'minMessage' => 'Votre email doit comporter au minimun {{ limit }} caracteres',
                        'maxMessage' => 'Votre email peux comporter au maximun {{ limit }} caracteres',
                    ]),
                    new Email([
                        'message' => "Votre email n 'est pas au bon format ex: mail@exemple.com"
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe', 'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide'
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom', 'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide'
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Civilité', 'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut etre vide'
                    ]),
                ],
                'expanded' => true,
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'validate' => false,
                'attr' => [
                    'class' => 'd-block mx-auto btn-primary col-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
