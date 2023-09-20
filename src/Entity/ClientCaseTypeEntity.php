<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientCaseTypeRepository")
 * @ORM\Table(name="client_case_type")
 * @ORM\HasLifecycleCallbacks()
 */

class ClientCaseTypeEntity extends BaseEntity
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
        
    /**
     * @ORM\Column(name="description", type="string")
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="ClientEntity", inversedBy="clientCaseTypes")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    protected $client;
    
    /**
     * @ORM\ManyToOne(targetEntity="CaseTypeEntity", inversedBy="clientCaseTypes")
     * @ORM\JoinColumn(name="case_type_id", referencedColumnName="id", nullable=true)
     */
    protected $caseType;

    public function __construct($data = null)
    {
    }

    /*--------------------------------------------------------------------------------------------------------*/
    /*					ClientCaseType Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/

/**
     * Set isDeleted
     * 
     * @param boolean $isDeleted
     *
     * @return ClientCaseTypeEntity
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

    public function getClient(): ?ClientEntity
    {
        return $this->client;
    }

    public function setClient(?ClientEntity $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getCaseType(): ?CaseTypeEntity
    {
        return $this->caseType;
    }

    public function setCaseType(?CaseTypeEntity $caseType): self
    {
        $this->caseType = $caseType;

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

   
}
