<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 * @ORM\Table(name="client")
 * @ORM\HasLifecycleCallbacks()
 */

class ClientEntity extends BaseEntity
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
     * @ORM\Column(name="patient_first_name", type="string", nullable=true)
     */
    protected $patientFirstName;

    /**
     * @ORM\Column(name="patient_middle_name", type="string", nullable=true)
     */
    protected $patientMiddleName;

    /**
     * @ORM\Column(name="patient_last_name", type="string", nullable=true)
     */
    protected $patientLastName;

    /**
     * @ORM\Column(name="birth_place", type="string", nullable=true)
     */
    protected $birthPlace;

    /**
     * @ORM\Column(name="case_no", type="string", nullable=true)
     */
    protected $caseNo;
    
    /**
     * @ORM\Column(name="case_type", type="string", nullable=true)
     */
    protected $caseType;

    /**
     * @ORM\Column(name="approve_amount", type="decimal", precision=12, scale=2, nullable=true)
     */
    protected $approveAmount;

    /**
     * @ORM\Column(name="assistant_type", type="string", nullable=true)
     */
    protected $assistantType;

    /**
     * @ORM\Column(name="treatment", type="string", nullable=true)
     */
    protected $treatment;

    /**
     * @ORM\Column(name="worker", type="string", nullable=true)
     */
    protected $worker;
    
    /**
     * @ORM\Column(name="educational_attainment", type="string", nullable=true)
     */
    protected $educationalAttainment;

    /**
     * @ORM\Column(name="income", type="string", nullable=true)
     */
    protected $income;

    /**
     * @ORM\Column(name="years_from_current_city", type="string", nullable=true)
     */
    protected $yearsFromCurrentCity;

    /**
     * @ORM\Column(name="residency_length", type="string", nullable=true)
     */
    protected $residencyLength;

    /**
     * @ORM\Column(name="gender", type="string", nullable=true)
     */
    protected $gender;

    /**
     * @ORM\Column(name="intake_date", type="datetime", nullable=true)
     */
    protected $intakeDate;

    /**
     * @ORM\Column(name="release_date", type="datetime", nullable=true)
     */
    protected $releaseDate;

    /**
     * @ORM\Column(name="birth_date", type="datetime", nullable=true)
     */
    protected $birthDate;

    /**
     * @ORM\Column(name="civil_status", type="string", nullable=true)
     */
    protected $civilStatus;

    /**
     * @ORM\Column(name="specified_civil_status", type="string", nullable=true)
     */
    protected $specifiedCivilStatus;

    /**
     * @ORM\Column(name="specified_case_type", type="string", nullable=true)
     */
    protected $specifiedCaseType;
    
    /**
     * @ORM\Column(name="relation_to_patient", type="string", nullable=true)
     */
    protected $relationToPatient;

    /**
     * @ORM\Column(name="occupation", type="string", nullable=true)
     */
    protected $occupation;

    /**
     * @ORM\Column(name="religion", type="string", nullable=true)
     */
    protected $religion;

    /**
     * @ORM\Column(name="contact_no", type="string", nullable=true)
     */
    protected $contactNo;

    /**
     * @ORM\Column(name="problem_presented", type="text", nullable=true)
     */
    protected $problemPresented;

     /**
     * @ORM\Column(name="housing", type="string", nullable=true)
     */
    protected $housing;
    
     /**
     * @ORM\Column(name="housing_structure", type="string", nullable=true)
     */
    protected $housingStructure;
    
     /**
     * @ORM\Column(name="lot", type="string", nullable=true)
     */
    protected $lot;

    /**
    * @ORM\Column(name="lightning", type="string", nullable=true)
    */
   protected $lightning;

    /**
     * @ORM\ManyToOne(targetEntity="BranchEntity", inversedBy="clients")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    protected $branch;

    /**
     * @ORM\ManyToOne(targetEntity="BarangayEntity", inversedBy="clients")
     * @ORM\JoinColumn(name="barangay_id", referencedColumnName="id", nullable=true)
     */
    protected $barangay;

    /**
     * @ORM\ManyToOne(targetEntity="BarangayEntity", inversedBy="clientAddresses")
     * @ORM\JoinColumn(name="barangay_address_id", referencedColumnName="id", nullable=true)
     */
    protected $barangayAddress;
    
    /**
     * @ORM\ManyToOne(targetEntity="DeseaseEntity", inversedBy="clients")
     * @ORM\JoinColumn(name="desease_id", referencedColumnName="id", nullable=true)
     */
    protected $desease;

    /**
     * @ORM\OneToMany(targetEntity="ClientFamilyEntity", mappedBy="client", cascade={"remove"})
     */
    protected $clientFamilies;

    /**
     * @ORM\OneToMany(targetEntity="ClientCaseTypeEntity", mappedBy="client", cascade={"remove"})
     */
    protected $clientCaseTypes;

    /**
     * @ORM\OneToMany(targetEntity="ClientAssistanceTypeEntity", mappedBy="client", cascade={"remove"})
     */
    protected $clientAssistanceTypes;

     /**
     * @ORM\OneToMany(targetEntity="ClientLightingEntity", mappedBy="client", cascade={"remove"})
     */
    protected $clientLightings;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserEntity", inversedBy="encoderClients")
     * @ORM\JoinColumn(name="encoder_id", referencedColumnName="id", nullable=true)
     */
    protected $encoder;

    public function __construct()
    {
        $this->clientFamilies = new ArrayCollection();
        $this->clientCaseTypes = new ArrayCollection();
        $this->clientAssistanceTypes = new ArrayCollection();
        $this->clientLightings = new ArrayCollection();
    }

    
    /*--------------------------------------------------------------------------------------------------------*/
    /*					Client Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName() {

        return $this->firstName . ' ' . $this->lastName;
    }


/**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return ClientEntity
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get caseTypesInArray
     *
     *
     * @return array
     */
    public function getCaseTypesInArray()
    {

        $ct= [];

        foreach($this->getClientCaseTypes() as $k => $clientCaseType){
            if(!$clientCaseType->getisDeleted()){
                $ct[$k] = $clientCaseType->getDescription();

            }

        }

        return $ct;
    }

    /**
     * Get assistanceTypesInArray
     *
     *
     * @return array
     */
    public function getAssistanceTypesInArray()
    {

        $ct= [];

        foreach($this->getClientAssistanceTypes() as $k => $clientAssistanceType){
            if(!$clientAssistanceType->getisDeleted()){
                $ct[$k] = $clientAssistanceType->getDescription();

            }

        }

        return $ct;
    }

    /**
     * Get lightingsInArray
     *
     *
     * @return array
     */
    public function getLightingsInArray()
    {

        $ct= [];

        foreach($this->getClientLightings() as $k => $clientLighting){
            if(!$clientLighting->getisDeleted()){
                $ct[$k] = $clientLighting->getDescription();

            }

        }

        return $ct;
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

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function setBirthPlace(?string $birthPlace): self
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function getEducationalAttainment(): ?string
    {
        return $this->educationalAttainment;
    }

    public function setEducationalAttainment(?string $educationalAttainment): self
    {
        $this->educationalAttainment = $educationalAttainment;

        return $this;
    }

    public function getIncome(): ?string
    {
        return $this->income;
    }

    public function setIncome(?string $income): self
    {
        $this->income = $income;

        return $this;
    }

    public function getResidencyLength(): ?string
    {
        return $this->residencyLength;
    }

    public function setResidencyLength(?string $residencyLength): self
    {
        $this->residencyLength = $residencyLength;

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

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

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

    public function getRelationToPatient(): ?string
    {
        return $this->relationToPatient;
    }

    public function setRelationToPatient(?string $relationToPatient): self
    {
        $this->relationToPatient = $relationToPatient;

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

    public function getReligion(): ?string
    {
        return $this->religion;
    }

    public function setReligion(?string $religion): self
    {
        $this->religion = $religion;

        return $this;
    }

    public function getContactNo(): ?string
    {
        return $this->contactNo;
    }

    public function setContactNo(?string $contactNo): self
    {
        $this->contactNo = $contactNo;

        return $this;
    }

    public function getProblemPresented(): ?string
    {
        return $this->problemPresented;
    }

    public function setProblemPresented(?string $problemPresented): self
    {
        $this->problemPresented = $problemPresented;

        return $this;
    }

    public function getHousing(): ?string
    {
        return $this->housing;
    }

    public function setHousing(?string $housing): self
    {
        $this->housing = $housing;

        return $this;
    }

    public function getHousingStructure(): ?string
    {
        return $this->housingStructure;
    }

    public function setHousingStructure(?string $housingStructure): self
    {
        $this->housingStructure = $housingStructure;

        return $this;
    }

    public function getLot(): ?string
    {
        return $this->lot;
    }

    public function setLot(?string $lot): self
    {
        $this->lot = $lot;

        return $this;
    }

    public function getLightning(): ?string
    {
        return $this->lightning;
    }

    public function setLightning(?string $lightning): self
    {
        $this->lightning = $lightning;

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

    public function getCaseNo(): ?string
    {
        return $this->caseNo;
    }

    public function setCaseNo(?string $caseNo): self
    {
        $this->caseNo = $caseNo;

        return $this;
    }

    public function getCaseType(): ?string
    {
        return $this->caseType;
    }

    public function setCaseType(?string $caseType): self
    {
        $this->caseType = $caseType;

        return $this;
    }

    public function getIntakeDate(): ?\DateTimeInterface
    {
        return $this->intakeDate;
    }

    public function setIntakeDate(?\DateTimeInterface $intakeDate): self
    {
        $this->intakeDate = $intakeDate;

        return $this;
    }

    public function getBarangay(): ?BarangayEntity
    {
        return $this->barangay;
    }

    public function setBarangay(?BarangayEntity $barangay): self
    {
        $this->barangay = $barangay;

        return $this;
    }

    public function getBarangayAddress(): ?BarangayEntity
    {
        return $this->barangayAddress;
    }

    public function setBarangayAddress(?BarangayEntity $barangayAddress): self
    {
        $this->barangayAddress = $barangayAddress;

        return $this;
    }

    /**
     * @return Collection<int, ClientFamilyEntity>
     */
    public function getClientFamilies(): Collection
    {
        return $this->clientFamilies;
    }

    public function addClientFamily(ClientFamilyEntity $clientFamily): self
    {
        if (!$this->clientFamilies->contains($clientFamily)) {
            $this->clientFamilies[] = $clientFamily;
            $clientFamily->setClient($this);
        }

        return $this;
    }

    public function removeClientFamily(ClientFamilyEntity $clientFamily): self
    {
        if ($this->clientFamilies->removeElement($clientFamily)) {
            // set the owning side to null (unless already changed)
            if ($clientFamily->getClient() === $this) {
                $clientFamily->setClient(null);
            }
        }

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
            $clientCaseType->setClient($this);
        }

        return $this;
    }

    public function removeClientCaseType(ClientCaseTypeEntity $clientCaseType): self
    {
        if ($this->clientCaseTypes->removeElement($clientCaseType)) {
            // set the owning side to null (unless already changed)
            if ($clientCaseType->getClient() === $this) {
                $clientCaseType->setClient(null);
            }
        }

        return $this;
    }

    public function getSpecifiedCivilStatus(): ?string
    {
        return $this->specifiedCivilStatus;
    }

    public function setSpecifiedCivilStatus(?string $specifiedCivilStatus): self
    {
        $this->specifiedCivilStatus = $specifiedCivilStatus;

        return $this;
    }

    public function getAssistantType(): ?string
    {
        return $this->assistantType;
    }

    public function setAssistantType(?string $assistantType): self
    {
        $this->assistantType = $assistantType;

        return $this;
    }


    public function getApproveAmount(): ?string
    {
        return $this->approveAmount;
    }

    public function setApproveAmount(?string $approveAmount): self
    {
        $this->approveAmount = str_replace(',', '', $approveAmount);

        return $this;
    }

    public function getPatientFirstName(): ?string
    {
        return $this->patientFirstName;
    }

    public function setPatientFirstName(?string $patientFirstName): self
    {
        $this->patientFirstName = $patientFirstName;

        return $this;
    }

    public function getPatientMiddleName(): ?string
    {
        return $this->patientMiddleName;
    }

    public function setPatientMiddleName(?string $patientMiddleName): self
    {
        $this->patientMiddleName = $patientMiddleName;

        return $this;
    }

    public function getPatientLastName(): ?string
    {
        return $this->patientLastName;
    }

    public function setPatientLastName(?string $patientLastName): self
    {
        $this->patientLastName = $patientLastName;

        return $this;
    }

    public function getTreatment(): ?string
    {
        return $this->treatment;
    }

    public function setTreatment(?string $treatment): self
    {
        $this->treatment = $treatment;

        return $this;
    }

    public function getDesease(): ?DeseaseEntity
    {
        return $this->desease;
    }

    public function setDesease(?DeseaseEntity $desease): self
    {
        $this->desease = $desease;

        return $this;
    }

    /**
     * @return Collection<int, ClientAssistanceTypeEntity>
     */
    public function getClientAssistanceTypes(): Collection
    {
        return $this->clientAssistanceTypes;
    }

    public function addClientAssistanceType(ClientAssistanceTypeEntity $clientAssistanceType): self
    {
        if (!$this->clientAssistanceTypes->contains($clientAssistanceType)) {
            $this->clientAssistanceTypes[] = $clientAssistanceType;
            $clientAssistanceType->setClient($this);
        }

        return $this;
    }

    public function removeClientAssistanceType(ClientAssistanceTypeEntity $clientAssistanceType): self
    {
        if ($this->clientAssistanceTypes->removeElement($clientAssistanceType)) {
            // set the owning side to null (unless already changed)
            if ($clientAssistanceType->getClient() === $this) {
                $clientAssistanceType->setClient(null);
            }
        }

        return $this;
    }

    public function getYearsFromCurrentCity(): ?string
    {
        return $this->yearsFromCurrentCity;
    }

    public function setYearsFromCurrentCity(?string $yearsFromCurrentCity): self
    {
        $this->yearsFromCurrentCity = $yearsFromCurrentCity;

        return $this;
    }

    /**
     * @return Collection<int, ClientLightingEntity>
     */
    public function getClientLightings(): Collection
    {
        return $this->clientLightings;
    }

    public function addClientLighting(ClientLightingEntity $clientLighting): self
    {
        if (!$this->clientLightings->contains($clientLighting)) {
            $this->clientLightings[] = $clientLighting;
            $clientLighting->setClient($this);
        }

        return $this;
    }

    public function removeClientLighting(ClientLightingEntity $clientLighting): self
    {
        if ($this->clientLightings->removeElement($clientLighting)) {
            // set the owning side to null (unless already changed)
            if ($clientLighting->getClient() === $this) {
                $clientLighting->setClient(null);
            }
        }

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getSpecifiedCaseType(): ?string
    {
        return $this->specifiedCaseType;
    }

    public function setSpecifiedCaseType(?string $specifiedCaseType): self
    {
        $this->specifiedCaseType = $specifiedCaseType;

        return $this;
    }

    public function getWorker(): ?string
    {
        return $this->worker;
    }

    public function setWorker(?string $worker): self
    {
        $this->worker = $worker;

        return $this;
    }

    public function getEncoder(): ?UserEntity
    {
        return $this->encoder;
    }

    public function setEncoder(?UserEntity $encoder): self
    {
        $this->encoder = $encoder;

        return $this;
    }





    

    

   
}
