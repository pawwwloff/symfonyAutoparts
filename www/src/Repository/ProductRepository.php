<?php


namespace App\Repository;


use App\Document\Product;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductRepository extends DocumentRepository
{

    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(Product::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param Product $product
     * @return Product
     */
    public function saveOne(Product $product) : Product
    {
        $this->dm->persist($product);
        $this->dm->flush();

        return $product;
    }

    /**
     * @param array $products
     * @return Product[]
     */
    public function saveArray(array $products) : array
    {
        foreach ($products as $product){
            $product->setId();
            $this->dm->persist($product);
        }
        $this->dm->flush();

        return $products;
    }

    /**
     * @param array $criteria
     * @param array|null $sort
     * @param int $limit
     * @param null $skip
     * @return Product[]
     */
    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $products = parent::findBy($criteria, $sort, $limit, $skip);

        return $products;
    }

    /**
     * @param string $id
     * @return Product
     */
    public function one(string $id) : Product
    {
        $product = parent::findOneBy(['id'=>$id]);

        if($product == null){
            throw new NotFoundHttpException("Товар {$id} не найден");
        }

        return $product;
    }

    /**
     * @param \MongoRegex $query
     * @return Product[]|array
     */
    public function search(\MongoRegex $query)
    {
        $products = $this->dm->getRepository(Product::class)->findBy(array('article' => $query));

        return $products;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function delete(Product $product) : bool
    {
        $this->dm->remove($product);

        return true;
    }

    /**
     * @param Product $product
     * @return Product
     */
    public function save(Product $product) : Product
    {
        $product->setId();

        $this->dm->persist($product);
        $this->dm->flush();

        return $product;
    }
}