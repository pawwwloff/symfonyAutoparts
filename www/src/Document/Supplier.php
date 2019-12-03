<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class Supplier
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
    * @MongoDB\Field(type="string")
    */
    protected $email;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $emailForPrice;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $emailTheme;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $status;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $searchFromXls;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $markup;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $deliveryTime;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $file;

    /**
     * Supplier constructor.
     * @param string $name
     */
    public function __construct()
    {

    }

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmailForPrice()
    {
        return $this->emailForPrice;
    }

    /**
     * @param mixed $emailForPrice
     */
    public function setEmailForPrice($emailForPrice): void
    {
        $this->emailForPrice = $emailForPrice;
    }

    /**
     * @return mixed
     */
    public function getEmailTheme()
    {
        return $this->emailTheme;
    }

    /**
     * @param mixed $emailTheme
     */
    public function setEmailTheme($emailTheme): void
    {
        $this->emailTheme = $emailTheme;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getSearchFromXls()
    {
        return $this->searchFromXls;
    }

    /**
     * @param mixed $searchFromXls
     */
    public function setSearchFromXls($searchFromXls): void
    {
        $this->searchFromXls = $searchFromXls;
    }

    /**
     * @return mixed
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * @param mixed $markup
     */
    public function setMarkup($markup): void
    {
        $this->markup = $markup;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * @param mixed $deliveryTime
     */
    public function setDeliveryTime($deliveryTime): void
    {
        $this->deliveryTime = $deliveryTime;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }


}