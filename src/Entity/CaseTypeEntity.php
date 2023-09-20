<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CaseTypeRepository")
 * @ORM\Table(name="case_type")
 * @ORM\HasLifecycleCallbacks()
 */

class CaseTypeEntity extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="BranchEntity", inversedBy="caseTypes")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    protected $branch;

      /**
     * @ORM\OneToMany(targetEntity="ClientCaseTypeEntity", mappedBy="caseType", cascade={"remove"})
     */
    protected $clientCaseTypes;


    public function __construct()
    {
        $this->clientCaseTypes = new ArrayCollection();
    }

    
    /*--------------------------------------------------------------------------------------------------------*/
    /*					CaseType Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/


    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return CaseTypeEntity
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

    public function getBranch(): ?BranchEntity
    {
        return $this->branch;
    }

    public function setBranch(?BranchEntity $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * @return Collection<int, ClientCaseTypeEntity>
     */
    public function getClientCaseTypes(): Collection
    {
        return $this->clientCaseTypes;
    }

    public function addClientCaseType(ClientCaseTypeEntity $clientCaseType): self
    {
        if (!$this->clientCaseTypes->contains($clientCaseType)) {
            $this->clientCaseTypes[] = $clientCaseType;
            $clientCaseType->setCaseType($this);
        }

        return $this;
    }

    public function removeClientCaseType(ClientCaseTypeEntity $clientCaseType): self
    {
        if ($this->clientCaseTypes->removeElement($clientCaseType)) {
            // set the owning side to null (unless already changed)
            if ($clientCaseType->getCaseType() === $this) {
                $clientCaseType->setCaseType(null);
            }
        }

        return $this;
    }

  
}
