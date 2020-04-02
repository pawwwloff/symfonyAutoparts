<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

/**
 * @MongoDB\Document
 * @MongoDB\Index(keys={"name"="text", "article"="text"})
 */
class Order
{
    /**
     * @MongoDB\Id(strategy="INCREMENT")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\User")
     */
    protected $user;

    /**
     * @Assert\NotBlank
     * @MongoDB\Field(type="string",nullable="false")
     */
    protected $fio;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @MongoDB\Field(type="string", nullable="false")
     */
    protected $email;

    /**
     * @Assert\NotBlank
     * @AssertPhoneNumber(type="mobile")
     * @MongoDB\Field(type="string", nullable="false")
     */
    protected $phone;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $summ;


    /**
     * @MongoDB\Field(type="float")
     */
    protected $baseSumm;

    /**
     * @MongoDB\Field(type="date", nullable="false")
     */
    protected $createAt;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->createAt = new \DateTime();
    }

    /**
     * @param mixed $fio
     */
    public function setFio($fio): void
    {
        $this->fio = $fio;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @param mixed $summ
     */
    public function setSumm($summ): void
    {
        $this->summ = $summ;
    }

    /**
     * @param mixed $baseSumm
     */
    public function setBaseSumm($baseSumm): void
    {
        $this->baseSumm = $baseSumm;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}