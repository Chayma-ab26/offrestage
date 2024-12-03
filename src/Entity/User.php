<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $skills = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $experiences = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $educations = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilsummary = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomentreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptionentreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secteuractivite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephoneentreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresseentreprise = null;

    /**
     * @var Collection<int, Candidature>
     */
    #[ORM\OneToMany(targetEntity: Candidature::class, mappedBy: 'etudiant')]
    private Collection $candidatures;

    /**
     * @var Collection<int, OffreStage>
     */
    #[ORM\OneToMany(targetEntity: OffreStage::class, mappedBy: 'entreprise')]
    private Collection $offresstage;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'destinataire')]
    private Collection $notifications;

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
        $this->offresstage = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSkills(): ?string
    {
        return $this->skills;
    }

    public function setSkills(?string $skills): static
    {
        $this->skills = $skills;

        return $this;
    }

    public function getExperiences(): ?string
    {
        return $this->experiences;
    }

    public function setExperiences(?string $experiences): static
    {
        $this->experiences = $experiences;

        return $this;
    }

    public function getEducations(): ?string
    {
        return $this->educations;
    }

    public function setEducations(?string $educations): static
    {
        $this->educations = $educations;

        return $this;
    }

    public function getProfilsummary(): ?string
    {
        return $this->profilsummary;
    }

    public function setProfilsummary(?string $profilsummary): static
    {
        $this->profilsummary = $profilsummary;

        return $this;
    }

    public function getNomentreprise(): ?string
    {
        return $this->nomentreprise;
    }

    public function setNomentreprise(?string $nomentreprise): static
    {
        $this->nomentreprise = $nomentreprise;

        return $this;
    }

    public function getDescriptionentreprise(): ?string
    {
        return $this->descriptionentreprise;
    }

    public function setDescriptionentreprise(?string $descriptionentreprise): static
    {
        $this->descriptionentreprise = $descriptionentreprise;

        return $this;
    }

    public function getSecteuractivite(): ?string
    {
        return $this->secteuractivite;
    }

    public function setSecteuractivite(?string $secteuractivite): static
    {
        $this->secteuractivite = $secteuractivite;

        return $this;
    }

    public function getTelephoneentreprise(): ?string
    {
        return $this->telephoneentreprise;
    }

    public function setTelephoneentreprise(?string $telephoneentreprise): static
    {
        $this->telephoneentreprise = $telephoneentreprise;

        return $this;
    }

    public function getAdresseentreprise(): ?string
    {
        return $this->adresseentreprise;
    }

    public function setAdresseentreprise(?string $adresseentreprise): static
    {
        $this->adresseentreprise = $adresseentreprise;

        return $this;
    }

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setEtudiant($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getEtudiant() === $this) {
                $candidature->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OffreStage>
     */
    public function getOffresstage(): Collection
    {
        return $this->offresstage;
    }

    public function addOffresstage(OffreStage $offresstage): static
    {
        if (!$this->offresstage->contains($offresstage)) {
            $this->offresstage->add($offresstage);
            $offresstage->setEntreprise($this);
        }

        return $this;
    }

    public function removeOffresstage(OffreStage $offresstage): static
    {
        if ($this->offresstage->removeElement($offresstage)) {
            // set the owning side to null (unless already changed)
            if ($offresstage->getEntreprise() === $this) {
                $offresstage->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setDestinataire($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getDestinataire() === $this) {
                $notification->setDestinataire(null);
            }
        }

        return $this;
    }
}
