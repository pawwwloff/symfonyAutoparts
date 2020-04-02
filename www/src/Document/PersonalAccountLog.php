<?php


namespace App\Document;


use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class PersonalAccountLog
{
    const CREATE = 0; //создание счета
    const PAID = 1; //оплата заказа
    const OVERDRAFT = 2; //установление нового овердрафта
    const FINANCE = 3; //пополнение счета
    const MANUAL = 4; //ручное изменение счета
    /**
     * @MongoDB\Id(strategy="INCREMENT")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\User")
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\PersonalAccount")
     */
    protected $personalAccount;

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

    /**
     * @MongoDB\Field(type="float")
     */
    protected $newOverdraft;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $newAvailable;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $newFinance;

    /**
     * @MongoDB\Field(type="date", nullable="false")
     */
    protected $updateDate;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $operation;


    /**
     * @MongoDB\Field(type="string")
     */
    protected $description;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $orderNumber;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->updateDate = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
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
    public function getPersonalAccount()
    {
        return $this->personalAccount;
    }

    /**
     * @param mixed $personalAccount
     */
    public function setPersonalAccount($personalAccount): void
    {
        $this->personalAccount = $personalAccount;
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
    public function getNewOverdraft()
    {
        return $this->newOverdraft;
    }

    /**
     * @param mixed $newOverdraft
     */
    public function setNewOverdraft($newOverdraft): void
    {
        $this->newOverdraft = $newOverdraft;
    }

    /**
     * @return mixed
     */
    public function getNewAvailable()
    {
        return $this->newAvailable;
    }


    public function setNewAvailable(): void
    {
        $this->newAvailable = $this->newFinance+$this->newOverdraft;
    }

    /**
     * @return mixed
     */
    public function getNewFinance()
    {
        return $this->newFinance;
    }

    /**
     * @param mixed $newFinance
     */
    public function setNewFinance($newFinance): void
    {
        $this->newFinance = $newFinance;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate(): \DateTime
    {
        return $this->updateDate;
    }

    /**
     * @param \DateTime $updateDate
     */
    public function setUpdateDate(\DateTime $updateDate): void
    {
        $this->updateDate = $updateDate;
    }

    /**
     * @return mixed
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param mixed $operation
     */
    public function setOperation($operation): void
    {
        $this->operation = $operation;
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

    public function setOrderNumber($orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return string
     */
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

}