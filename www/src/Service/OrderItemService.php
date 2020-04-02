<?php


namespace App\Service;


use App\Document\OrderItem;
use App\Document\PersonalAccount;
use App\Document\Product;
use App\Document\User;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderItemService
{
    /**
     * @var OrderItemRepository
     */
    private $orderItemRepository;
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var SupplierRepository
     */
    private $supplierRepository;
    /**
     * @var PersonalAccountService
     */
    private $personalAccountService;


    /**
     * ProductService constructor.
     * @param OrderItemRepository $orderItemRepository
     * @param ProductService $productService
     * @param SupplierRepository $supplierRepository
     * @param PersonalAccountService $personalAccountService
     */
    public function __construct(OrderItemRepository $orderItemRepository,
                                ProductService $productService,
                                SupplierRepository $supplierRepository,
                                PersonalAccountService $personalAccountService)
    {
        $this->productService = $productService;
        $this->supplierRepository = $supplierRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->personalAccountService = $personalAccountService;
    }

    public function addInCart($productId, $quantity, $user)
    {
        $product = $this->productService->one($productId);
        $order = null;
        $productQuantity = $product->getCount();
        if($quantity<=0){
            $order = $this->deleteFromCart($product, $productId, $user);
        }else {
            if ($product) {
                if ($order = $this->getOrderFromBasket($product, $user)) {
                    if ($quantity - $order->getQuantity() < $productQuantity) {
                        $order = $this->update($order, $product, $quantity);
                    } else {
                        throw new HttpException('401', 'Quantity dno');
                    }
                } else {
                    if ($productQuantity >= $quantity) {
                        $order = $this->add($product, $quantity, $user);
                    } else {
                        throw new HttpException('401', 'Quantity dno');
                    }
                }
            }
        }
        return $order;
    }

    public function add(Product $product, $quantity, $user)
    {
        $order = new OrderItem();
        $order->setProduct($product);
        $order->setName($product->getName());
        $order->setArticle($product->getArticle());
        $order->setVendor($product->getVendor());
        $order->setBasePrice($product->getPrice());
        $order->setQuantity($quantity);
        $order->setSupplier($product->getSupplier());
        $order->setUser($user);
        $order->setPrice();
        $order->setSumm();
        $product = $this->productService->updateCount($product, $product->getCount()-$quantity);
        return $this->orderItemRepository->save($order);
    }

    public function update(OrderItem $order, Product $product, $quantity){
        $oldQuantity = $order->getQuantity();
        $order->setBasePrice($product->getPrice());
        $order->setQuantity($quantity);
        $order->setPrice();
        $order->setSumm();
        $product = $this->productService->updateCount($product, $product->getCount()-$quantity+$oldQuantity);
        $this->orderItemRepository->save($order);

        return $order;
    }

    public function getOrderFromBasket($product, $user, $exists = false){
        return $this->orderItemRepository->findOneBy(['user'=>$user, 'product'=>$product, 'order'=>['$exists'=>$exists]]);
    }

    public function getForUser(User $user, $exists = false){
        return $this->orderItemRepository->list(['user'=>$user, 'order'=>['$exists'=>$exists]]);
    }

    public function getById($id){
        $item = $this->orderItemRepository->one($id);

        return $item;
    }

    public function getByIdAndUser(User $user, $id){
        $item = $this->orderItemRepository->one($id);
        if($item->getUser()==$user){
            return $item;
        }
        return false;
    }

    public function getCartArray($user){
        $orders = $this->getForUser($user);
        $cart = null;
        foreach ($orders as $order){
            $cart[$order->getProduct()->getId()] = $order->getQuantity();
        }
        return $cart;
    }

    public function getCart($user){
        $cart = ['summ' => 0];
        $orders = $this->getForUser($user);
        foreach ($orders as $orderItem){
            $cart['summ']+=$orderItem->getSumm();
        }
        return $cart;
    }

    private function deleteFromCart(Product $product, $productId, $user)
    {
        $order = $this->orderItemRepository->findOneBy(['user'=>$user, 'product.$id'=>$productId]);

        if($product){
            $product = $this->productService->updateCount($product, $product->getCount()+$order->getQuantity());
        }
        return $this->orderItemRepository->delete($order);
    }

    public function paid(OrderItem $order, PersonalAccount $personalAccount)
    {
        $sum = $order->getSumm();
        $finance = $personalAccount->getFinance();
        $overdraft = $personalAccount->getOverdraft();
        $available = $finance+$overdraft;
        $result = false;
        if($sum<=$available){
            if($finance>=$sum){
                $order->setPaid($sum, 0);
            }else{
                $inOverdraft = $sum - ($finance>0?$finance:0);
                $order->setPaid($sum, $inOverdraft);
            }
            $order->setStatus(OrderItem::RECEIVED_IN_ORDER);
            $result = $this->orderItemRepository->save($order);
        }
        return $result;
    }

    public function chengeStatusPaid(OrderItem $order, PersonalAccount $personalAccount, $status)
    {
        $sum = $order->getSumm();
        $finance = $personalAccount->getFinance();
        $overdraft = $personalAccount->getOverdraft();
        if($finance>=$sum){
            $order->setPaid($sum, 0);
        }else{
            $inOverdraft = $sum - ($finance>0?$finance:0);
            $order->setPaid($sum, $inOverdraft);
        }
        $order->setStatus($status);
        $result = $this->orderItemRepository->save($order);

        return $result;
    }

    public function changeQuantity(OrderItem $order, User $user, $quantity = 0)
    {
        $paid = $order->getPaid();
        $result = true;
        $personalAccount = $order->getUser()->getPersonalAccount();
        if($quantity>0 && $quantity != $order->getQuantity()) {
            if ($paid == OrderItem::PAID_FULL || $paid == OrderItem::PAID_OVER) {
                $sum = $order->getSumm();                           //2000
                $newSum = $order->getPrice() * $quantity;           //1000
                $finance = $personalAccount->getFinance() + $sum;   //-5000 + 2000 = -3000
                $inOverdraft = $newSum - ($finance > 0 ? $finance : 0);   //1000
                $order->setQuantity($quantity);
                $order->setSumm();
                $order->setPaid($newSum, $inOverdraft);
                $result = $this->orderItemRepository->save($order);
                if ($result) {
                    return [
                        'order' => $order,
                        'personalAccount' => $personalAccount,
                        'user' => $user,
                        'oldSum' => $sum,
                        'changeAccount' => true
                    ];

                }
            } elseif ($quantity != $order->getQuantity()) {
                $order->setQuantity($quantity);
                $order->setSumm();
                $result = $this->orderItemRepository->save($order);
            }
        }
        return $result;
    }

    public function changePrice(OrderItem $order, User $user, $price = 0)
    {
        $paid = $order->getPaid();
        $result = true;
        $personalAccount = $order->getUser()->getPersonalAccount();
        if($paid==OrderItem::PAID_FULL || $paid==OrderItem::PAID_OVER){
            $sum = $order->getSumm();                           //2000
            $newSum = $price * $order->getQuantity();           //1000
            $finance = $personalAccount->getFinance() + $sum;   //-5000 + 2000 = -3000
            $inOverdraft = $newSum - ($finance>0?$finance:0);   //1000
            $order->setCustomPrice($price);
            $order->setSumm();
            $order->setPaid($newSum, $inOverdraft);
            $result = $this->orderItemRepository->save($order);
            if($result){
                return [
                    'order'=>$order,
                    'personalAccount'=>$personalAccount,
                    'user'=>$user,
                    'oldSum'=>$sum,
                    'changeAccount'=>true
                ];

            }
        }elseif($price!=$order->getPrice()){
            $order->setCustomPrice($price);
            $order->setSumm();
            $result = $this->orderItemRepository->save($order);
        }

        return $result;
    }

    public function splitOrder(OrderItem $oldOrder, $newQuantity){

        $paQuery = $this->orderItemRepository->createQueryBuilder()
            ->field('order')->equals($oldOrder->getOrder())
            ->sort('number', 'desc')
            ->getQuery()
            ->getSingleResult();
        $newOrder = clone $oldOrder;
        $newNumber = $paQuery->getNumber()+1;
        $newOrder->setNumber($newNumber);
        $newOrder->unsetId();
        $paid = $oldOrder->getPaid();
        $result = true;
        $personalAccount = $oldOrder->getUser()->getPersonalAccount();
        $quantity = $oldOrder->getQuantity();
        $oldQuantity = $quantity - $newQuantity;         //3 - 2 = 1
        $sum = $oldOrder->getSumm();                                    //3000
        $newSumForOldOrder = $oldOrder->getPrice() * $oldQuantity;      //1000
        $newSumForNewOrder = $oldOrder->getPrice() * $newQuantity;      //2000
        if($paid==OrderItem::PAID_OVER){
            $finance = $personalAccount->getFinance() + $sum;               //-1000 + 3000 = 2000
            $oldInOverdraft = $newSumForOldOrder - ($finance>0?$finance:0);
            $finance -= $newSumForOldOrder;                                 //2000 - 1000 = 1000
            $newInOverdraft = $newSumForNewOrder - ($finance>0?$finance:0);
            $oldOrder->setQuantity($oldQuantity);
            $oldOrder->setSumm();
            $oldOrder->setPaid($newSumForOldOrder, $oldInOverdraft);
            $resultOld = $this->orderItemRepository->save($oldOrder);
            if($resultOld) {
                $newOrder->setQuantity($newQuantity);
                $newOrder->setSumm();
                $newOrder->setPaid($newSumForNewOrder, $newInOverdraft);
                $resultNew = $this->orderItemRepository->save($newOrder);
                if($resultNew){
                    return [
                        'oldOrder'=>$oldOrder,
                        'newOrder'=>$newOrder,
                    ];
                }else{
                    $finance = $personalAccount->getFinance() + $sum;
                    $oldOrder->setQuantity($quantity);
                    $oldOrder->setSumm();
                    $oldOrder->setPaid($oldOrder->getPrice() * $quantity, $sum - ($finance>0?$finance:0));
                    $result = $this->orderItemRepository->save($oldOrder);
                }
                return $result;
            }
        }else{
            $oldOrder->setQuantity($oldQuantity);
            $oldOrder->setSumm();
            $resultOld = $this->orderItemRepository->save($oldOrder);
            if($resultOld) {
                $newOrder->setQuantity($newQuantity);
                $newOrder->setSumm();
                $resultNew = $this->orderItemRepository->save($newOrder);
                if($resultNew){
                    return [
                        'oldOrder'=>$oldOrder,
                        'newOrder'=>$newOrder,
                    ];
                }else{
                    $oldOrder->setQuantity($quantity);
                    $oldOrder->setSumm();
                    $result = $this->orderItemRepository->save($oldOrder);
                }
                return $result;
            }
        }

        return null;
    }

    public function changeStatus(OrderItem $order, User $user, $status){
        $oldStatus = $order->getStatus();
        $operation = OrderItem::getStatusChangeOperation($oldStatus, $status);
        if($operation == OrderItem::OPERATION_NOTHING){
            if($oldStatus!=$status){
                $order->setStatus($status);
                $this->orderItemRepository->save($order);
            }
            return $order;
        }
        $result = $order;
        $paid = $order->getPaid();
        $personalAccount = $order->getUser()->getPersonalAccount();
        if($operation == OrderItem::OPERATION_PAID && $paid==OrderItem::NOT_PAID){
            $paidResult = $this->chengeStatusPaid($order, $personalAccount, $status);
            if($paidResult) {
                $this->personalAccountService->changeStatus($order, $personalAccount, $user, $oldStatus, $operation);
            }
        }elseif($operation == OrderItem::OPERATION_RETURN && ($paid==OrderItem::PAID_FULL || $paid==OrderItem::PAID_OVER)){
            $order->setPaid(0);
            $order->setStatus($status);
            $result = $this->orderItemRepository->save($order);
            if($result) {
                $this->personalAccountService->changeStatus($order, $personalAccount, $user, $oldStatus, $operation);
            }
        }

        return $result;
    }

    public function changeSupplier(OrderItem $order, $supplier){
        $oldSupplier = $order->getSupplier();
        if($supplier != $oldSupplier){
            $order->setSupplier($supplier);
            $result = $this->orderItemRepository->save($order);
            return $result;
        }

        return null;
    }
}