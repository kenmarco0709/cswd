<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientFamilyRepository")
 * @ORM\Table(name="client_family")
 * @ORM\HasLifecycleCallbacks()
 */

class ClientFamilyEntity extends BaseEntity
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     */
    protected $middleName;

    /**
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    protected $lastName;
    
    /**
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    protected $gender;
    
    /**
     * @ORM\Column(name="family_role", type="string", nullable=true)
     */
    protected $familyRole;

    /**
     * @ORM\Column(name="age", type="string", nullable=true)
     */
    protected $age;

    /**
     * @ORM\Column(name="civil_status", type="string", nullable=true)
     */
    protected $civilStatus;

    /**
     * @ORM\Column(name="educational_attainment", type="string", nullable=true)
     */
    protected $educationAttainment;

    /**
     * @ORM\Column(name="occupation", type="string", nullable=true)
     */
    protected $occupation;
    
    /**
     * @ORM\Column(name="monthly_income", type="string", nullable=true)
     */
    protected $monthlyIncome;

    /**
     * @ORM\ManyToOne(targetEntity="ClientEntity", inversedBy="clientFamilies")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true)
     */
    protected $client;

    public function __construct($data = null)
    {
    }

    /*--------------------------------------------------------------------------------------------------------*/
    /*					ClientFamily Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/

/**
     * Set isDeleted
     * 
     * @param boolean $isDeleted
     *
     * @return ClientFamilyEntity
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

   /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName() {

        return $this->firstName . ' ' . $this->middleName .' '. $this->lastName;
    }

    /*--------------------------------------------------------------------------------------------------------*/
    /*					    Defined Setters and Getters													      */
    /*--------------------------------------------------------------------------------------------------------*/

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFamilyRole(): ?string
    {
        return $this->familyRole;
    }

    public function setFamilyRole(?string $familyRole): self
    {
        $this->familyRole = $familyRole;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(?string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getCivilStatus(): ?string
    {
        return $this->civilStatus;
    }

    public function setCivilStatus(?string $civilStatus): self
    {
        $this->civilStatus = $civilStatus;

        return $this;
    }

    public function getEducationAttainment(): ?string
    {
        return $this->educationAttainment;
    }

    public function setEducationAttainment(?string $educationAttainment): self
    {
        $this->educationAttainment = $educationAttainment;

        return $this;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(?string $occupation): self
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getMonthlyIncome(): ?string
    {
        return $this->monthlyIncome;
    }

    public function setMonthlyIncome(?string $monthlyIncome): self
    {
        $this->monthlyIncome = $monthlyIncome;

        return $this;
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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }




   
}
