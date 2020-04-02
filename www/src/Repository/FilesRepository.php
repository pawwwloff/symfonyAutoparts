<?php


namespace App\Repository;


use App\Document\Files;
use App\Document\Order;
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
class FilesRepository extends DocumentRepository
{

    /**
     * SupplierRepository constructor.
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(Files::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $suppliers = parent::findBy($criteria, $sort, $limit, $skip);

        return $suppliers;
    }

    public function save(Files $file) : Files
    {
        $this->dm->persist($file);
        $this->dm->flush();

        return $file;
    }
}