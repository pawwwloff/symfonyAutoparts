<?php


namespace App\Service;


use App\Document\Order;
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
     * @var ProductService
     */
    private $productService;
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
    public function __construct(OrderRepository $orderRepository, ProductService $productService, SupplierRepository $supplierRepository)
    {
        $this->productService = $productService;
        $this->supplierRepository = $supplierRepository;
        $this->orderRepository = $orderRepository;
    }

    public function addInCart($productId, $quantity, $user, $sessionId)
    {
        $product = $this->productService->one($productId);
        $order = null;
        $productQuantity = $product->getCount();
        if($quantity<=0){
            $order = $this->deleteFromCart($product, $productId, $user, $sessionId);
        }else {
            if ($product) {
                if ($order = $this->getOrderFromBasket($product, $user, $sessionId)) {
                    if ($quantity - $order->getQuantity() < $productQuantity) {
                        $order = $this->update($order, $product, $quantity);
                    } else {
                        throw new HttpException('401', 'Quantity dno');
                    }
                } else {
                    if ($productQuantity >= $quantity) {
                        $order = $this->add($product, $quantity, $user, $sessionId);
                    } else {
                        throw new HttpException('401', 'Quantity dno');
                    }
                }
            }
        }
        return $order;
    }

    public function add(Product $product, $quantity, $user, $sessionId)
    {
        $order = new Order();
        $order->setProduct($product);
        $order->setName($product->getName());
        $order->setArticle($product->getArticle());
        $order->setVendor($product->getVendor());
        $order->setBasePrice($product->getPrice());
        $order->setQuantity($quantity);
        $order->setSupplier($product->getSupplier());
        $order->setUser($user);
        $order->setSession($sessionId);
        $order->setPrice();
        $order->setSumm();
        $product = $this->productService->updateCount($product, $product->getCount()-$quantity);
        return $this->orderRepository->save($order);
    }

    public function update(Order $order, Product $product, $quantity){
        $oldQuantity = $order->getQuantity();
        $order->setBasePrice($product->getPrice());
        $order->setQuantity($quantity);
        $order->setPrice();
        $order->setSumm();
        $product = $this->productService->updateCount($product, $product->getCount()-$quantity+$oldQuantity);
        $this->orderRepository->save($order);

        return $order;
    }

    public function getOrderFromBasket($product, $user, $sessionId){
        return $this->orderRepository->findOneBy(['user'=>$user, 'session'=>$sessionId, 'product'=>$product]);
    }

    public function getForUser(User $user){
        return $this->orderRepository->list(['user'=>$user]);
    }

    public function getForSession(string $sessionId){
        return $this->orderRepository->list(['user'=>null, 'session'=>$sessionId]);
    }

    private function deleteFromCart(Product $product, $productId, $user, $sessionId)
    {
        $order = $this->orderRepository->findOneBy(['user'=>$user, 'session'=>$sessionId, 'product.$id'=>$productId]);
        if($product){
            $product = $this->productService->updateCount($product, $product->getCount()+$order->getQuantity());
        }
        return $this->orderRepository->delete($order);
    }


}