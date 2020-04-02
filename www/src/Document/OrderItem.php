<?php


namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 * @MongoDB\Index(keys={"name"="text", "article"="text"})
 */
class OrderItem
{
    const PAID_FULL = 2;
    const PAID_OVER = 1;
    const NOT_PAID = 0;

    const OPERATION_NOTHING = 0; //ничего не делаем с заказом
    const OPERATION_PAID = 1; //Оплачиваем заказ с внутреннего счета, и записываем операцию в логи
    const OPERATION_RETURN = 2;  //Отменяем оплату заказа, возвращаем на счет

    const WAITING_FOR_PAYMENT = 'WAITING_FOR_PAYMENT';
    const RECEIVED_IN_ORDER = 'RECEIVED_IN_ORDER';
    const CLOSED = 'CLOSED';
    const NOT_AVAILABLE = 'NOT_AVAILABLE';
    const POSTED = 'POSTED';
    const STORE = 'STORE';
    const IN_TRANSIT = 'IN_TRANSIT';
    const COMPLETED = 'COMPLETED';
    const PURCHASED_BY_SUPPLIER = 'PURCHASED_BY_SUPPLIER';
    const SHIPPED = 'SHIPPED';
    const IN_PROCESSING = 'IN_PROCESSING';

    protected $statuses = [
        'WAITING_FOR_PAYMENT'=>'Ждет оплаты',
        'RECEIVED_IN_ORDER'=>'Получено в заказ',
        'CLOSED'=>'Отменен',
        'NOT_AVAILABLE'=>'Нет в наличии',
        'POSTED'=>'Размещен',
        'STORE'=>'Пришло на склад',
        'IN_TRANSIT'=>'В пути',
        'COMPLETED'=>'Выполнено',
        'PURCHASED_BY_SUPPLIER'=>'Закуплено поставщиком',
        'SHIPPED'=>'Отгружен',
        'IN_PROCESSING'=>'В обработке',
    ];
    /**
     * @MongoDB\Id(strategy="INCREMENT")
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
     * @MongoDB\Field(type="string")
     */
    protected $status;

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
     * @MongoDB\Field(type="int")
     */
    protected $paid;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $overdraft;

    protected $paidText;

    /**
     * Product constructor.
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    public function paidOver(){
        $this->paid = 1;
    }

    public function paidFull(){
        $this->paid = 2;
    }

    public function notPaid(){
        $this->paid = 0;
    }

   /* public function setId()
    {
        $arFields = [$this->name, $this->vendor, $this->article];
        if($this->supplier instanceof Supplier){
            $arFields[] = $this->supplier->getId();
        }
        $this->id = hash("sha256", implode('.',$arFields));
    }*/
   public function unsetId(){
       $this->id = null;
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
     * @param mixed $price
     */
    public function setCustomPrice($price): void
    {
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

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order): void
    {
        $this->order = $order;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return mixed
     */
    public function getSumm()
    {
        return $this->summ;
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
    public function getPaid()
    {
        $paid = self::NOT_PAID;
        switch ($this->paid) {
            case self::PAID_FULL:
                $paid = self::PAID_FULL;
                break;

            case self::PAID_OVER:
                $paid = self::PAID_OVER;
                break;
        }
        return $paid;
    }

    /**
     * @return mixed
     */
    public function getPaidText()
    {
        $return = 'Не оплачено';
        switch ($this->paid) {
            case self::PAID_FULL:
                $return = 'Оплачено';
                break;

            case self::PAID_OVER:
                $return = 'Оплачено в овердрафт';
                break;
        }
        $this->paidText = $return;
        return $this->paidText;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $sum
     * @param $overdraft
     */
    public function setPaid($sum, $overdraft = 0): void
    {
        if($sum>0){
            if($overdraft>0){
                $this->paid = self::PAID_OVER;
                $this->overdraft = $overdraft;
            }else{
                $this->paid = self::PAID_FULL;
                $this->overdraft = 0;
            }
        }else{
            $this->paid = self::NOT_PAID;
            $this->overdraft = 0;
        }
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return $this->statuses[$this->status];
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        if(key_exists($status, $this->statuses)){
            $this->status = $status;
        }
    }

    /**
     * @return array
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public static function getStatusChangeOperation($oldStatus, $newStatus){
        $operation = self::OPERATION_NOTHING;
        if(!$oldStatus){
            $oldStatus = self::WAITING_FOR_PAYMENT;
        }
        if($oldStatus==$newStatus){
            return $operation;
        }
        switch ($oldStatus){
            case self::WAITING_FOR_PAYMENT:             /** Если был "Ждет оплаты" */
                switch ($newStatus){
                    case self::RECEIVED_IN_ORDER:               /** Если чтал Получено в заказ  - оплачиваем заказ */
                    case self::STORE:                           /** Если стал Пришло на склад - оплачиваем */
                    case self::IN_TRANSIT:                      /** Если стал В пути - оплачиваем */
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                    case self::POSTED:                          /** Если стал Размещен - то видимо оплачиваем TODO - проверить */
                        $operation = self::OPERATION_PAID;
                        break;
                    case self::CLOSED:                          /** Если стал Отменен - ничего не делаем */
                    case self::NOT_AVAILABLE:                   /** Если стал Нет в наличии то ничего не делаем */
                        $operation = self::OPERATION_NOTHING;
                        break;

                }
                break;
            case self::RECEIVED_IN_ORDER:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                    case self::POSTED:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
            case self::CLOSED:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_NOTHING;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_PAID;
                        break;
                }
                break;
            case self::NOT_AVAILABLE:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                        $operation = self::OPERATION_NOTHING;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_PAID;
                        break;
                }
                break;
            case self::POSTED:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
            case self::STORE:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
            case self::IN_TRANSIT:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::STORE:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
            case self::COMPLETED:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
            case self::PURCHASED_BY_SUPPLIER:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::SHIPPED:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
            case self::SHIPPED:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::IN_PROCESSING:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
            case self::IN_PROCESSING:
                switch ($newStatus){
                    case self::WAITING_FOR_PAYMENT:
                    case self::CLOSED:
                    case self::NOT_AVAILABLE:
                        $operation = self::OPERATION_RETURN;
                        break;
                    case self::RECEIVED_IN_ORDER:
                    case self::POSTED:
                    case self::STORE:
                    case self::IN_TRANSIT:
                    case self::COMPLETED:
                    case self::PURCHASED_BY_SUPPLIER:
                    case self::SHIPPED:
                        $operation = self::OPERATION_NOTHING;
                        break;
                }
                break;
        }

        return $operation;
    }

}