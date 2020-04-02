<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Files
{
    /** @var array Поля для селекта при загрузке файла */
    const FIELD_SELECT = [
        'article'=>'Артикул',
        'vendor'=>'Производитель',
        'count'=>'Количество',
        'price'=>'Цена',
        'name'=>'Наименование'
    ];
    /**
     * @MongoDB\Id(strategy="INCREMENT")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\Supplier")
     */
    protected $supplier;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $active;

    /**
    * @Assert\NotBlank()
    * @MongoDB\Field(type="string")
    */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @MongoDB\Field(type="string")
     */
    protected $emailTheme;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $searchFromXls;

    /**
     * @Assert\NotBlank()
     * @MongoDB\Field(type="int")
     */
    protected $markup;

    /**
     * @Assert\NotBlank()
     * @MongoDB\Field(type="int", nullable="false")
     */
    protected $deliveryTime;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $file;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $jsonTable;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $jsonSettings;

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

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getJsonTable()
    {
        return $this->jsonTable;
    }

    public function getJsonTableArray()
    {
        return json_decode($this->jsonTable, true);
    }

    /**
     * @param mixed $jsonTable
     */
    public function setJsonTable($jsonTable): void
    {
        $this->jsonTable = $jsonTable;
    }

    /**
     * @return mixed
     */
    public function getJsonSettings()
    {
        return json_decode($this->jsonSettings, true);
    }

    /**
     * @param mixed $jsonSettings
     */
    public function setJsonSettings($jsonSettings): void
    {
        $this->jsonSettings = json_encode($jsonSettings);
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

    /**
     * @return mixed
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param mixed $supplier
     */
    public function setSupplier($supplier): void
    {
        $this->supplier = $supplier;
    }

}