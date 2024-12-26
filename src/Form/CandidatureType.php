<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Candidature;

class CandidatureType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('cv', FileType::class, [
'label' => 'Téléchargez votre CV',
'required' => true,
'mapped' => false,
'attr' => ['accept' => '.pdf, .doc, .docx'],
])
->add('lettreDeMotivation', FileType::class, [
'label' => 'Téléchargez votre lettre de motivation',
'required' => true,
'mapped' => false,
'attr' => ['accept' => '.pdf, .doc, .docx'],
    ])
    ->add('status', ChoiceType::class, [
        'choices' => [
            'En attente' => 'pending',
            'Accepté' => 'accepted',
            'Refusé' => 'rejected',
        ],
        'label' => 'Statut',
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => Candidature::class,
]);
}
}
