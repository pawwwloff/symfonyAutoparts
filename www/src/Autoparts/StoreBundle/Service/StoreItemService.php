<?php


namespace App\Autoparts\StoreBundle\Service;


use App\Autoparts\StoreBundle\Document\StoreItem;
use App\Autoparts\StoreBundle\Repository\StoreItemRepository;
use App\Document\Product;
use App\Autoparts\StoreBundle\Document\ReceiptOrderItem;
use App\Repository\ProductRepository;
use App\Autoparts\StoreBundle\Repository\ReceiptOrderItemRepository;
use App\Repository\SupplierRepository;
use App\Service\OrderItemService;

class StoreItemService
{


    /**
     * @var StoreItemRepository
     */
    private $storeItemRepository;

    public function __construct(StoreItemRepository $storeItemRepository)
    {


        $this->storeItemRepository = $storeItemRepository;
    }


    public function addArray($arFields){
        $storeItems = [];
        foreach ($arFields as $arField){
            $storeItem = new StoreItem();
            $storeItem->setShipment($arField['shipped']);
            $storeItem->setReceiptOrderItem($arField['receiptOrderItem']);
            $storeItem->setStatus($arField['status']);

            $storeItems[] = $storeItem;
        }
        if(count($storeItems)>0) {
            return $this->storeItemRepository->saveArray($storeItems);
        }
    }

    public function addFromReceipt($arReceiptOrderItems){
        $storeItems = [];
        foreach ($arReceiptOrderItems as $arReceiptOrderItem){
            $storeItem = new StoreItem();
            $storeItem->setReceiptOrderItem($arReceiptOrderItem);
            $storeItem->setStatus(StoreItem::STATUS_NOT_SHIPPED);

            $storeItems[] = $storeItem;
        }
        if(count($storeItems)>0) {
            return $this->storeItemRepository->saveArray($storeItems);
        }
    }
}