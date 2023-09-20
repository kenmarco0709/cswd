<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeseaseRepository")
 * @ORM\Table(name="desease")
 * @ORM\HasLifecycleCallbacks()
 */

class DeseaseEntity extends BaseEntity
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="code", type="string", nullable=true)
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
     * @ORM\OneToMany(targetEntity="ClientEntity", mappedBy="desease", cascade={"remove"})
     */
    protected $clients;


    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    
    /*--------------------------------------------------------------------------------------------------------*/
    /*					Desease Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/


    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return DeseaseEntity
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
     * @return Collection<int, ClientEntity>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(ClientEntity $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setDesease($this);
        }

        return $this;
    }

    public function removeClient(ClientEntity $client): self
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getDesease() === $this) {
                $client->setDesease(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

   
  
}
