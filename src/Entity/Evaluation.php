<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column]
    private ?int $bareme = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Professor $professor = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassLevel $classLevel = null;

    #[ORM\OneToMany(mappedBy: 'evaluation', targetEntity: Grade::class, orphanRemoval: true)]
    private Collection $grades;

    #[ORM\OneToMany(mappedBy: 'evaluation', targetEntity: Categorie::class)]
    private Collection $categorie;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
        $this->categorie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getBareme(): ?int
    {
        return $this->bareme;
    }

    public function setBareme(int $bareme): static
    {
        $this->bareme = $bareme;

        return $this;
    }

    public function getProfessor(): ?Professor
    {
        return $this->professor;
    }

    public function setProfessor(?Professor $professor): static
    {
        $this->professor = $professor;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getClassLevel(): ?ClassLevel
    {
        return $this->classLevel;
    }

    public function setClassLevel(?ClassLevel $classLevel): static
    {
        $this->classLevel = $classLevel;

        return $this;
    }

    /**
     * @return Collection<int, Grade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grade $grade): static
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setEvaluation($this);
        }

        return $this;
    }

    public function removeGrade(Grade $grade): static
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getEvaluation() === $this) {
                $grade->setEvaluation(null);
            }
        }

        return $this;
    }

    public function getGradeByStudent(Student $student): ?Grade
    {
        foreach ($this->getGrades() as $grade){
            if($grade->getStudent() === $student){
                return $grade;
            }
        }
        return null;
    }

    public function calulMoyenne(): ?float
    {
        $grades = $this->getGrades();

        if ($grades->isEmpty()) {
            return null;
        }
        $totalPoints = 0;
        $totalEvaluations = 0;

        foreach ($grades as $grade) {
            $totalPoints += $grade->getGrade();
            $totalEvaluations++;
        }

        return ($totalEvaluations > 0) ? $totalPoints / $totalEvaluations : null;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): static
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie->add($categorie);
            $categorie->setEvaluation($this);
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): static
    {
        if ($this->categorie->removeElement($categorie)) {
            // set the owning side to null (unless already changed)
            if ($categorie->getEvaluation() === $this) {
                $categorie->setEvaluation(null);
            }
        }

        return $this;
    }
}
