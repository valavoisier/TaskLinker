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

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Employee implements UserInterface, PasswordAuthenticatedUserInterface
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
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
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
}
