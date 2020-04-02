<?php


namespace App\Repository;


use App\Document\PersonalAccountLog;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonalAccountLogRepository extends DocumentRepository
{

    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(PersonalAccountLog::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param array $criteria
     * @param array|null $sort
     * @param int $limit
     * @param null $skip
     * @return PersonalAccountLog[]
     */
    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $orderItems = parent::findBy($criteria, $sort, $limit, $skip);

        return $orderItems;
    }



    /**
     * @param PersonalAccountLog $personalAccountLog
     * @return PersonalAccountLog
     */
    public function save(PersonalAccountLog $personalAccountLog) : PersonalAccountLog
    {
        $this->dm->persist($personalAccountLog);
        $this->dm->flush();

        return $personalAccountLog;
    }
}