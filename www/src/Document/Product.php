<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 * @MongoDB\Index(keys={"name"="text", "article"="text"})
 */
class Product
{

    /**
     * @MongoDB\Id(strategy="NONE")
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string",nullable="false")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string", nullable="false")
     */
    protected $article;

    /**
     * @MongoDB\Field(type="string", nullable="false")
     */
    protected $vendor;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\Supplier")
     */
    protected $supplier;

    /**
     * @MongoDB\Field(type="float", nullable="false")
     */
    protected $price;

    /**
     * @MongoDB\Field(type="int", nullable="false")
     */
    protected $count;

    protected $quantity;


    /**
     * Product constructor.
     */
    public function __construct()
    {
    }


    public function setId()
    {
        $arFields = [$this->name, $this->vendor, $this->article];
        if($this->supplier instanceof Supplier){
            $arFields[] = $this->supplier->getId();
        }
        $this->id = hash("sha256", implode('.',$arFields));
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
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param mixed $article
     */
    public function setArticle($article): void
    {
        $this->article = $article;
    }

    /**
     * @return mixed
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param mixed $vendor
     */
    public function setVendor($vendor): void
    {
        $this->vendor = $vendor;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
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
    public function setSupplier(Supplier $supplier): void
    {
        $this->supplier = $supplier;
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }


}