<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BarangayRepository")
 * @ORM\Table(name="barangay")
 * @ORM\HasLifecycleCallbacks()
 */

class BarangayEntity extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="CityEntity", inversedBy="barangays")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id", nullable=true)
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="ClientEntity", mappedBy="barangay", cascade={"remove"})
     */
    protected $clients;

    /**
     * @ORM\OneToMany(targetEntity="ClientEntity", mappedBy="barangayAddress", cascade={"remove"})
     */
    protected $clientAddresses;


    public function __construct($data = null)
    {
        $this->clients = new ArrayCollection();
        $this->clientAddresses = new ArrayCollection();
    }

    /*--------------------------------------------------------------------------------------------------------*/
    /*					Barangay Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/

/**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return BarangayEntity
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

    public function getCity(): ?CityEntity
    {
        return $this->city;
    }

    public function setCity(?CityEntity $city): self
    {
        $this->city = $city;

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
            $client->setBarangay($this);
        }

        return $this;
    }

    public function removeClient(ClientEntity $client): self
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getBarangay() === $this) {
                $client->setBarangay(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ClientEntity>
     */
    public function getClientAddresses(): Collection
    {
        return $this->clientAddresses;
    }

    public function addClientAddress(ClientEntity $clientAddress): self
    {
        if (!$this->clientAddresses->contains($clientAddress)) {
            $this->clientAddresses[] = $clientAddress;
            $clientAddress->setBarangayAddress($this);
        }

        return $this;
    }

    public function removeClientAddress(ClientEntity $clientAddress): self
    {
        if ($this->clientAddresses->removeElement($clientAddress)) {
            // set the owning side to null (unless already changed)
            if ($clientAddress->getBarangayAddress() === $this) {
                $clientAddress->setBarangayAddress(null);
            }
        }

        return $this;
    }

  
}
