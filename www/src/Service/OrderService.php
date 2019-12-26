<?php


namespace App\Service;


use App\Document\OrderItem;
use App\Document\Product;
use App\Document\User;
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
     * ProductService constructor.
     * @param OrderRepository $orderRepository
     * @param ProductService $productService
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(OrderRepository $orderRepository, OrderItemService $orderItemService, SupplierRepository $supplierRepository)
    {
        $this->orderItemService = $orderItemService;
        $this->supplierRepository = $supplierRepository;
        $this->orderRepository = $orderRepository;
    }

    public function add($fio, $email, $phone, $user)
    {
        $order = new Order();
        $order->setFio($fio);
        $order->setEmail($email);
        $order->setPhone($phone);
        $order->setUser($user);
        $basket = $this->orderItemService->getForUser($user);
        dd($basket);
        /*
         foreach($basket as $item){
         if($item->product->count>0){

        }
        }
         * */
        //$order->setBaseSumm($baseSumm);
        //$order->setSumm($summ);
        //$product = $this->productService->updateCount($product, $product->getCount()-$quantity);
        return $this->orderRepository->save($order);
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