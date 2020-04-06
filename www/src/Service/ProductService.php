<?php


namespace App\Service;


use App\Document\Product;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;

class ProductService
{

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var SupplierRepository
     */
    private $supplierRepository;

    /**
     * ProductService constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository, SupplierRepository $supplierRepository)
    {
        $this->productRepository = $productRepository;
        $this->supplierRepository = $supplierRepository;
    }

    public function add($name, $article, $vendor, $price, $count, $supplierId)
    {
        $product = new Product();
        $product->setName($name);
        $product->setArticle($article);
        $product->setVendor($vendor);
        $product->setPrice($price);
        $product->setCount($count);
        $supplier = $this->supplierRepository->one($supplierId);
        if($supplier) {
            $product->setSupplier($supplier);
        }


        return $this->productRepository->save($product);
    }

    public function addArray($arFields, $supplierId){

        $supplier = $this->supplierRepository->one($supplierId);
        $products = [];
        foreach ($arFields as $arField){
            $product = new Product();
            $product->setName($arField['name']);
            $product->setArticle($arField['article']);
            $product->setVendor($arField['vendor']);
            $product->setPrice($arField['price']);
            $product->setCount($arField['count']);
            if($supplier) {
                $product->setSupplier($supplier);
            }
            $products[] = $product;
        }
        if(count($products)>0) {
            return $this->productRepository->saveArray($products);
        }
    }

    public function one(string $id) : Product
    {
        $product = $this->productRepository->one($id);

        return $product;
    }

    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $products = $this->productRepository->list($criteria, $sort, $limit, $skip);
        return $products;
    }

    public function search(string $query) : array
    {
        $query = new \MongoRegex("/$query/i");
        return $this->productRepository->search($query);
    }

    public function updateCount(Product $product, $count){
        //$product->setCount($count);
        //return $this->productRepository->save($product);
        return $product;
    }
}