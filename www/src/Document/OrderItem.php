<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 * @MongoDB\Index(keys={"name"="text", "article"="text"})
 */
class OrderItem
{
    /**
     * @MongoDB\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\Order")
     */
    protected $order;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $number;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\User")
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\Product")
     */
    protected $product;

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
    protected $quantity;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $summ;

    /**
     * @MongoDB\Field(type="float", nullable="false")
     */
    protected $basePrice;

    /**
     * @MongoDB\Field(type="date", nullable="false")
     */
    protected $updatedAt;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime();
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
     * @param User $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
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
    public function setPrice($procent = 10): void
    {
        $price = $this->basePrice + $this->basePrice*$procent/100;
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
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
     * @param mixed $productId
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }

    /**
     * @param mixed $summ
     */
    public function setSumm(): void
    {
        $summ = $this->price*$this->quantity;
        $this->summ = $summ;
    }

    /**
     * @param mixed $basePrice
     */
    public function setBasePrice($basePrice): void
    {
        $this->basePrice = $basePrice;
    }

}