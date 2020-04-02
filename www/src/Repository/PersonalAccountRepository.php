<?php


namespace App\Repository;


use App\Document\PersonalAccount;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonalAccountRepository extends DocumentRepository
{

    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(PersonalAccount::class);
        parent::__construct($dm, $uow, $classMetaData);
    }



    /**
     * @param PersonalAccount $personalAccount
     * @return PersonalAccount
     */
    public function save(PersonalAccount $personalAccount) : PersonalAccount
    {
        $this->dm->persist($personalAccount);
        $this->dm->flush();

        return $personalAccount;
    }
}