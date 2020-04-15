<?php


namespace App\Autoparts\StoreBundle\Repository;


use App\Autoparts\StoreBundle\Document\StoreItem;
use App\Document\Product;
use App\Document\Supplier;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SupplierRepository
 * @package App\Repository
 */
class StoreItemRepository extends DocumentRepository
{

    /**
     * SupplierRepository constructor.
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(StoreItem::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param StoreItem $storeItem
     * @return StoreItem
     */
    public function save(StoreItem $storeItem) : StoreItem
    {
        $this->dm->persist($storeItem);
        $this->dm->flush();

        return $storeItem;
    }

    /**
     * @param array $storeItems
     * @return StoreItem[]
     */
    public function saveArray(array $storeItems) : array
    {
        foreach ($storeItems as $storeItem){
            $this->dm->persist($storeItem);
        }
        $this->dm->flush();

        return $storeItems;
    }

    /**
     * @param array $criteria
     * @param array|null $sort
     * @param int $limit
     * @param null $skip
     * @return StoreItem[]
     */
    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $storeItems = parent::findBy($criteria, $sort, $limit, $skip);

        return $storeItems;
    }

    public function one(int $id) : StoreItem
    {
        $storeItem = parent::findOneBy(['id'=>$id]);

        if($storeItem == null){
            throw new NotFoundHttpException("Элемент {$id} не найден");
        }

        return $storeItem;
    }
}