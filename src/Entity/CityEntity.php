<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @ORM\Table(name="city")
 * @ORM\HasLifecycleCallbacks()
 */

class CityEntity extends BaseEntity
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
     * @ORM\ManyToOne(targetEntity="ProvinceEntity", inversedBy="cities")
     * @ORM\JoinColumn(name="province_id", referencedColumnName="id", nullable=true)
     */
    protected $province;

    public function __construct($data = null)
    {
    }

    /*--------------------------------------------------------------------------------------------------------*/
    /*					City Defined Setters and Getters													  */
    /*--------------------------------------------------------------------------------------------------------*/

/**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     *
     * @return CityEntity
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

    public function getProvince(): ?ProvinceEntity
    {
        return $this->province;
    }

    public function setProvince(?ProvinceEntity $province): self
    {
        $this->province = $province;

        return $this;
    }   
}
