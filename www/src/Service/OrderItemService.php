<?php


namespace App\Service;


use App\Document\OrderItem;
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
     * ProductService constructor.
     * @param OrderItemRepository $orderItemRepository
     * @param ProductService $productService
     * @param SupplierRepository $supplierRepository
     */
    public function __construct(OrderItemRepository $orderItemRepository, ProductService $productService, SupplierRepository $supplierRepository)
    {
        $this->productService = $productService;
        $this->supplierRepository = $supplierRepository;
        $this->orderItemRepository = $orderItemRepository;
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

    public function getOrderFromBasket($product, $user){
        return $this->orderItemRepository->findOneBy(['user'=>$user, 'product'=>$product]);
    }

    public function getForUser(User $user){
        return $this->orderItemRepository->list(['user'=>$user]);
    }

    private function deleteFromCart(Product $product, $productId, $user)
    {
        $order = $this->orderItemRepository->findOneBy(['user'=>$user, 'product.$id'=>$productId]);

        if($product){
            $product = $this->productService->updateCount($product, $product->getCount()+$order->getQuantity());
        }
        return $this->orderItemRepository->delete($order);
    }


}