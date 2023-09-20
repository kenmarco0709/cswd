<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BranchRepository")
 * @ORM\Table(name="branch")
 * @ORM\HasLifecycleCallbacks()
 */

class BranchEntity extends BaseEntity
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="code", type="string")
     */
    protected $code;
    
    /**
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="CompanyEntity", inversedBy="branches")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id", nullable=true)
     */
    protected $company;

    /**
     * @ORM\OneToMany(targetEntity="UserEntity", mappedBy="branch", cascade={"remove"})
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="BranchVariableEntity", mappedBy="branch", cascade={"remove"})
     */
    protected $branchVariables;
    
    /**
     * @ORM\OneToMany(targetEntity="CaseTypeEntity", mappedBy="branch", cascade={"remove"})
     */
    protected $caseTypes;

    public function __construct($data = null)
    {
        $this->users = new ArrayCollection();
        $this->branchVariables = new ArrayCollection();
        $this->caseTypes = new ArrayCollection();
     
    }

    /*--------------------------------------------------------------------------------------------------------*/
    /*					Branch Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/

/**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return BranchEntity
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
    /*--------------------------------------------------------------------------------------------------------*/
    /*					    Defined Setters and Getters													      */
    /*--------------------------------------------------------------------------------------------------------*/

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCompany(): ?CompanyEntity
    {
        return $this->company;
    }

    public function setCompany(?CompanyEntity $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, UserEntity>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserEntity $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setBranch($this);
        }

        return $this;
    }

    public function removeUser(UserEntity $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getBranch() === $this) {
                $user->setBranch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BranchVariableEntity>
     */
    public function getBranchVariables(): Collection
    {
        return $this->branchVariables;
    }

    public function addBranchVariable(BranchVariableEntity $branchVariable): self
    {
        if (!$this->branchVariables->contains($branchVariable)) {
            $this->branchVariables[] = $branchVariable;
            $branchVariable->setBranch($this);
        }

        return $this;
    }

    public function removeBranchVariable(BranchVariableEntity $branchVariable): self
    {
        if ($this->branchVariables->removeElement($branchVariable)) {
            // set the owning side to null (unless already changed)
            if ($branchVariable->getBranch() === $this) {
                $branchVariable->setBranch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CaseTypeEntity>
     */
    public function getCaseTypes(): Collection
    {
        return $this->caseTypes;
    }

    public function addCaseType(CaseTypeEntity $caseType): self
    {
        if (!$this->caseTypes->contains($caseType)) {
            $this->caseTypes[] = $caseType;
            $caseType->setBranch($this);
        }

        return $this;
    }

    public function removeCaseType(CaseTypeEntity $caseType): self
    {
        if ($this->caseTypes->removeElement($caseType)) {
            // set the owning side to null (unless already changed)
            if ($caseType->getBranch() === $this) {
                $caseType->setBranch(null);
            }
        }

        return $this;
    }

 
}
