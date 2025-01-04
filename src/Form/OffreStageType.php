<?php

namespace App\Form;

use App\Entity\OffreStage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; // Importer SubmitType

class OffreStageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('datePublication', null, [
                'widget' => 'single_text',
            ])
            ->add('dateExpiration', null, [
                'widget' => 'single_text',
            ])

            ->add('typecontrat')
            ->add('entreprise', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nomentreprise',
            ]);
        $builder->add('status', ChoiceType::class, [
            'choices' => [
                'Ouverte' => 'ouverte',
                'Fermée' => 'fermée',
            ],
            'data' => 'ouverte', // Valeur par défaut
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OffreStage::class,
        ]);
    }
}
