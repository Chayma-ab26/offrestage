<?php

namespace App\Form;

use App\Entity\Candidature;
use App\Entity\OffreStage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('dateSoumission', null, [
                'widget' => 'single_text',
            ])
            ->add('cv')
            ->add('lettreDeMotivation')
            ->add('etudiant', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('offreStage', EntityType::class, [
                'class' => OffreStage::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}
