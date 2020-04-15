<?php


namespace App\Autoparts\StoreBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class StoreItem
{
    const STATUSES = [
        'SHIPPED'=>'Отгружено',
        'PART_SHIPPED'=>'Частично отгружено',
        'NOT_SHIPPED'=>'Не отгружено'
    ];
    const STATUSES_TO_SHIPMENT = [
        'SHIPPED', 'PART_SHIPPED'
    ];

    const STATUS_NOT_SHIPPED = 'NOT_SHIPPED';
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
     * @MongoDB\ReferenceOne(targetDocument="App\Autoparts\StoreBundle\Document\Shipment", nullable="false")
     */
    protected $shipment;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $quantityShipped;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $status;

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
    public function getQuantityShipped()
    {
        return $this->quantityShipped;
    }

    /**
     * @param mixed $quantityShipped
     */
    public function setQuantityShipped($quantityShipped): void
    {
        $this->quantityShipped = $quantityShipped;
    }

    /**
     * @return mixed
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * @param mixed $shipment
     */
    public function setShipment($shipment): void
    {
        $this->shipment = $shipment;
    }

    /**
     * @return mixed
     */
    public function getReceiptOrderItem()
    {
        return $this->receiptOrderItem;
    }

    /**
     * @param mixed $receiptOrderItem
     */
    public function setReceiptOrderItem($receiptOrderItem): void
    {
        $this->receiptOrderItem = $receiptOrderItem;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


}