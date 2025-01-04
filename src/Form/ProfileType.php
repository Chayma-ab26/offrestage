<?php

namespace App\Form;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['data'];  // Récupère l'utilisateur pour vérifier son rôle

        // Champs communs à tous les utilisateurs

        // Vérifie si l'utilisateur est une entreprise
        if (in_array('ROLE_ENTREPRISE', $user->getRoles())) {
            $builder
                ->add('nomentreprise', TextType::class, [
                    'label' => 'Nom de l\'entreprise'
                ])
                ->add('descriptionentreprise', TextareaType::class, [
                    'label' => 'Description de l\'entreprise'
                ])
                ->add('secteuractivite', ChoiceType::class, [
                    'label' => 'Secteur d\'activité',
                    'choices' => [
                        'Informatique' => 'informatique',
                        'Commerce' => 'commerce',
                        'Santé' => 'sante',
                        'Finance' => 'finance',

                        // Ajoutez d'autres secteurs ici
                    ],
                    'expanded' => false, // Par défaut, c'est une liste déroulante
                    'multiple' => false, // Une seule option peut être sélectionnée
                ])
                ->add('telephoneentreprise', TextType::class, [
                    'label' => 'Téléphone de l\'entreprise'
                ])
                ->add('adresseentreprise', TextType::class, [
                    'label' => 'Adresse de l\'entreprise'
                ]);
        }

        // Vérifie si l'utilisateur est un étudiant
        if (in_array('ROLE_ETUDIANT', $user->getRoles())) {
            $builder
                ->add('nom', TextType::class, [
                    'label' => 'Nom',
                    'required' => true,

                ])
                ->add('prenom', TextType::class, [
                    'label' => 'Prénom',
                    'required' => true,

                ])

            ->add('educations', TextType::class, [
                    'label' => 'Éducation'
                ])
                ->add('skills', TextareaType::class, [
                    'label' => 'Compétences'
                ])
                ->add('experiences', TextareaType::class, [
                    'label' => 'Expériences'
                ]);
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Mettre à jour le profil'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}