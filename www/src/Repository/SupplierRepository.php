<?php


namespace App\Repository;


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
class SupplierRepository extends DocumentRepository
{

    /**
     * SupplierRepository constructor.
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(Supplier::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param Supplier $supplier
     * @return Supplier
     */
    public function save(Supplier $supplier) : Supplier
    {
        $this->dm->persist($supplier);
        $this->dm->flush();

        return $supplier;
    }

    /**
     * @param array $criteria
     * @param array|null $sort
     * @param int $limit
     * @param null $skip
     * @return Supplier[]
     */
    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $suppliers = parent::findBy($criteria, $sort, $limit, $skip);

        return $suppliers;
    }

    public function one(int $id) : Supplier
    {
        $supplier = parent::findOneBy(['id'=>$id]);

        if($supplier == null){
            throw new NotFoundHttpException("Поставщик {$id} не найден");
        }

        return $supplier;
    }
}