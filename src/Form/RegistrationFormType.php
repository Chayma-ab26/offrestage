<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('email')
->add('roles', ChoiceType::class, [
"choices" => [
"ROLE_ETUDIANT" => "ROLE_ETUDIANT",
"ROLE_ENTREPRISE" => "ROLE_ENTREPRISE"
]
])
->add('agreeTerms', CheckboxType::class, [
'mapped' => false,
'constraints' => [
new IsTrue([
'message' => 'You should agree to our terms.',
]),
],
])
->add('plainPassword', PasswordType::class, [
'mapped' => false,
'attr' => ['autocomplete' => 'new-password'],
'constraints' => [
new NotBlank([
'message' => 'Please enter a password',
]),
new Length([
'min' => 6,
'minMessage' => 'Your password should be at least {{ limit }} characters',
'max' => 4096,
]),
],
])
// Ajout des champs supplémentaires pour "nom", "prenom", etc.
->add('nom', TextType::class, [
'required' => false,  // Vous pouvez ajuster la visibilité selon le besoin
])
->add('prenom', TextType::class, [
'required' => false,
])
->add('skills', TextareaType::class, [
'required' => false,
'attr' => ['placeholder' => 'List your skills here'],
])
->add('experiences', TextareaType::class, [
'required' => false,
'attr' => ['placeholder' => 'Add your work experiences here'],
])
->add('educations', TextareaType::class, [
'required' => false,
'attr' => ['placeholder' => 'Your educational background'],
])
->add('nomentreprise', TextType::class, [
'required' => false,
])
->add('descriptionentreprise', TextareaType::class, [
'required' => false,
'attr' => ['placeholder' => 'Describe your company'],
])
->add('secteurActivite', TextType::class, [
'required' => false,
])
->add('telephoneentreprise', TextType::class, [
'required' => false,
])
->add('adresseentreprise', TextareaType::class, [
'required' => false,
])
->add('profilsummary', TextareaType::class, [
'required' => false,
'attr' => ['placeholder' => 'Provide a brief profile summary'],
])
;

// Transformer le champ 'roles' pour qu'il soit un tableau
$builder->get('roles')
->addModelTransformer(new CallbackTransformer(
function ($rolesAsArray): string {
return implode(',', $rolesAsArray);  // Conversion du tableau en chaîne
},
function ($rolesAsString): array {
return explode(',', $rolesAsString);  // Conversion de la chaîne en tableau
}
));
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'data_class' => User::class,
]);
}
}
