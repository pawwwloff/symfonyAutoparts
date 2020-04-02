<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

/**
 * @MongoDB\Document
 * @MongoDB\Index(keys={"name"="text"})
 */
class PersonalAccount
{
    /**
     * @MongoDB\Id(strategy="INCREMENT")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string", nullable="false")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $overdraft;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $available;


    /**
     * @MongoDB\Field(type="float")
     */
    protected $finance;

    protected $description;
    /**
     * Product constructor.
     */
    public function __construct()
    {

    }


    /**
     * @return mixed
     */
    public function getOverdraft()
    {
        return $this->overdraft;
    }

    /**
     * @param mixed $overdraft
     */
    public function setOverdraft($overdraft): void
    {
        $this->overdraft = $overdraft;
    }

    /**
     * @return mixed
     */
    public function getAvailable()
    {
        return $this->available;
    }


    public function setAvailable(): void
    {
        $this->available = $this->finance+$this->overdraft;
    }

    /**
     * @return mixed
     */
    public function getFinance()
    {
        return $this->finance;
    }

    /**
     * @param mixed $finance
     */
    public function setFinance($finance): void
    {
        $this->finance = $finance;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

}