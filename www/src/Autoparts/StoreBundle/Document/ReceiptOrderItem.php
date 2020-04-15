<?php


namespace App\Autoparts\StoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class ReceiptOrderItem
{
    /**
     * @MongoDB\Id(strategy="INCREMENT")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Autoparts\StoreBundle\Document\Receipt", nullable="false")
     */
    protected $receipt;

    /**
     * @MongoDB\ReferenceOne(targetDocument="App\Document\OrderItem", nullable="false")
     */
    protected $orderItem;

    /**
     * @MongoDB\Field(type="int", nullable="false")
     */
    protected $quantity;

    /**
     * @MongoDB\Field(type="date", nullable="false")
     */
    protected $create;

    /**
     * @return mixed
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param mixed $orderItem
     */
    public function setOrderItem($orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @return mixed
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @param mixed $receipt
     */
    public function setReceipt($receipt): void
    {
        $this->receipt = $receipt;
    }

    /**
     * @return mixed
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * @param mixed $create
     */
    public function setCreate(): void
    {
        $this->create = new \DateTime();
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}