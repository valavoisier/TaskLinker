<?php

namespace App\Entity;

use App\Enum\EmployeeStatus;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Employee implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")] 
    #[Assert\Length(
        min: 2, 
        max: 255, 
        minMessage: "Le prénom doit contenir au moins {{ limit }} caractères.", 
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")] 
    #[Assert\Length(
        min: 2, 
        max: 255, 
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.", 
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")] 
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date d'entrée est obligatoire.")] 
    #[Assert\LessThanOrEqual("today", message: "La date d'entrée ne peut pas être postérieure à aujourd'hui.")]
    private ?\DateTimeInterface $entryDate = null;

    #[ORM\Column(type: 'string', enumType: EmployeeStatus::class)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    private ?EmployeeStatus $status = null;

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

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'employees')]
    private Collection $projects;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'employee')]
    private Collection $tasks;

    #[ORM\Column(type: Types::TEXT, nullable: true)] 
    private ?string $googleAuthenticatorSecret = null;
    #[ORM\Column(type: 'boolean')]
    private bool $isTwoFactorEnabled = false;
    #[ORM\Column(name: 'hide2_faprompt', type: 'boolean')]
    private bool $hide2FAPrompt = false;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entryDate;
    }

    public function setEntryDate(?\DateTimeInterface $entryDate): static
    {
        $this->entryDate = $entryDate;

        return $this;
    }

    public function getStatus(): ?EmployeeStatus
    {
        return $this->status;
    }

    public function setStatus(?EmployeeStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Retourne les initiales de l'employé (première lettre du prénom + première lettre du nom).
     */
    public function getInitials(): string
    {
        $firstInitial = $this->firstname ? mb_strtoupper(mb_substr($this->firstname, 0, 1)) : '';
        $lastInitial = $this->lastname ? mb_strtoupper(mb_substr($this->lastname, 0, 1)) : '';

        return $firstInitial . $lastInitial;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
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
    public function setRoles(array|string $roles): static
    {
        // Si un seul rôle est envoyé (string), on le transforme en tableau 
        if (is_string($roles)) { 
            $roles = [$roles];
            }
        $this->roles = $roles;
        return $this;
    }

    /**
     * Retourne le rôle principal (pour le formulaire)
     * Retourne ROLE_ADMIN si présent, sinon ROLE_USER
     * string car on veut un seul rôle pour le formulaire, même si en base on peut en avoir plusieurs (ex: ROLE_ADMIN et ROLE_USER)
     */
    public function getMainRole(): string
    {
        return in_array('ROLE_ADMIN', $this->roles) ? 'ROLE_ADMIN' : 'ROLE_USER';
    }

    /**
     * Définit le rôle principal (pour le formulaire)
     * Si ROLE_ADMIN est sélectionné, on met ROLE_ADMIN en base, sinon ROLE_USER
     * string car on reçoit un seul rôle du formulaire, même si en base on peut en avoir plusieurs (ex: ROLE_ADMIN et ROLE_USER)
     */
    public function setMainRole(string $role): static
    {
        if ($role === 'ROLE_ADMIN') {
            $this->roles = ['ROLE_ADMIN'];
        } else {
            $this->roles = ['ROLE_USER'];
        }
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
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'entryDate' => $this->entryDate,
            'status' => $this->status,
            'roles' => $this->roles,
            'password' => hash('crc32c', $this->password),
            'isTwoFactorEnabled' => $this->isTwoFactorEnabled,
            'hide2FAPrompt' => $this->hide2FAPrompt,
        ];
    }

    /**
     * Restore the user from serialized data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
        $this->email = $data['email'];
        $this->entryDate = $data['entryDate'];
        $this->status = $data['status'];
        $this->roles = $data['roles'];
        $this->password = $data['password'];
        $this->isTwoFactorEnabled = $data['isTwoFactorEnabled'] ?? false;
        $this->hide2FAPrompt = $data['hide2FAPrompt'] ?? false;
        
        // Réinitialiser les collections
        $this->projects = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * Retourne les projets associés à l'employé.
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    /** 
     * Ajoute un projet à l'employé.
     */
    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addEmployee($this);
        }

        return $this;
    }

    /** 
     * Retire un projet de l'employé.
     */
    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            $project->removeEmployee($this);
        }

        return $this;
    }

    /**
     * Retourne les tâches assignées à l'employé.
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setEmployee($this);
        }

        return $this;
    }

    /** 
     * Retire une tâche de l'employé.
     */
    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getEmployee() === $this) {
                $task->setEmployee(null);
            }
        }

        return $this;
    }
  /* 2FA Google Authenticator */ 
    public function isGoogleAuthenticatorEnabled(): bool 
    {
        // Désactiver la 2FA en dev si la variable d'environnement est définie
        if (isset($_ENV['BYPASS_2FA']) && $_ENV['BYPASS_2FA'] === '1') {
            return false;
        }
        
        return null !== $this->googleAuthenticatorSecret && $this->isTwoFactorEnabled; 
    } 
    
    public function getGoogleAuthenticatorUsername(): string 
    { 
        return $this->email; 
    } 
    
    public function getGoogleAuthenticatorSecret(): ?string 
    { 
        return $this->googleAuthenticatorSecret; 
    } 
        
    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void 
    { 
        $this->googleAuthenticatorSecret = $googleAuthenticatorSecret; 
    }
    
    public function isTwoFactorEnabled(): bool
    {
        return $this->isTwoFactorEnabled;
    }

    public function setIsTwoFactorEnabled(bool $isTwoFactorEnabled): void
    {
        $this->isTwoFactorEnabled = $isTwoFactorEnabled;
    }

    public function getHide2FAPrompt(): bool
    {
        return $this->hide2FAPrompt;
    }

    public function setHide2FAPrompt(bool $hide2FAPrompt): void
    {
        $this->hide2FAPrompt = $hide2FAPrompt;
    }
  }
