<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

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
     * @MongoDB\Field(type="string",nullable="false")
     */
    protected $fio;

    /**
     * @MongoDB\Field(type="string", nullable="false")
     */
    protected $email;

    /**
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

}