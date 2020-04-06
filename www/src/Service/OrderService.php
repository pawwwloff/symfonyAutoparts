<?php


namespace App\Service;


use App\Document\Order;
use App\Document\OrderItem;
use App\Document\Product;
use App\Document\User;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderService
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var OrderItemService
     */
    private $orderItemService;
    /**
     * @var SupplierRepository
     */
    private $supplierRepository;
    /**
     * @var OrderItemRepository
     */
    private $orderItemRepository;


    /**
     * ProductService constructor.
     * @param OrderRepository $orderRepository
     * @param ProductService $productService
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(OrderRepository $orderRepository, OrderItemService $orderItemService,
                                SupplierRepository $supplierRepository, OrderItemRepository $orderItemRepository)
    {
        $this->orderItemService = $orderItemService;
        $this->supplierRepository = $supplierRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function add($fio, $email, $phone, $user)
    {
        $basket = $this->orderItemService->getForUser($user);
        if($basket) {
            $order = new Order();
            $order->setFio($fio);
            $order->setEmail($email);
            $order->setPhone($phone);
            $order->setUser($user);
            if($this->orderRepository->save($order)){
                foreach($basket as $number => $item){
                    if($item->getProduct()->getCount()>0){
                        $item->setOrder($order);
                        /** TODO если средств на счете достаточно, заказ создается со статусом «Получено в заказ»*/
                        $item->setStatus(OrderItem::WAITING_FOR_PAYMENT);
                        $item->setNumber($number+1);
                        $this->orderItemRepository->save($item);
                    }
                }
            }
        }
        return $order;
        /*

         * */
        //$order->setBaseSumm($baseSumm);
        //$order->setSumm($summ);
        //$product = $this->productService->updateCount($product, $product->getCount()-$quantity);
        //return ;
    }

    /*public function update(OrderItem $order, Product $product, $quantity){
        $oldQuantity = $order->getQuantity();
        $order->setBasePrice($product->getPrice());
        $order->setQuantity($quantity);
        $order->setPrice();
        $order->setSumm();
        //$product = $this->productService->updateCount($product, $product->getCount()-$quantity+$oldQuantity);
        $this->orderRepository->save($order);

        return $order;
    }*/

    public function getForUser(User $user){
        return $this->orderRepository->list(['user'=>$user]);
    }

}