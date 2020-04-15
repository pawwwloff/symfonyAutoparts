<?php


namespace App\Autoparts\StoreBundle\Repository;


use App\Autoparts\StoreBundle\Document\ReceiptOrderItem;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReceiptOrderItemRepository extends DocumentRepository
{

    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(ReceiptOrderItem::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param ReceiptOrderItem $receiptOrderItem
     * @return ReceiptOrderItem
     */
    public function saveOne(ReceiptOrderItem $receiptOrderItem) : ReceiptOrderItem
    {
        $this->dm->persist($receiptOrderItem);
        $this->dm->flush();

        return $receiptOrderItem;
    }

    /**
     * @param array $receiptOrderItems
     * @return ReceiptOrderItem[]
     */
    public function saveArray(array $receiptOrderItems) : array
    {
        foreach ($receiptOrderItems as $receiptOrderItem){
            $this->dm->persist($receiptOrderItem);
        }
        $this->dm->flush();

        return $receiptOrderItems;
    }

    /**
     * @param array $criteria
     * @param array|null $sort
     * @param int $limit
     * @param null $skip
     * @return ReceiptOrderItem[]
     */
    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $receiptOrderItems = parent::findBy($criteria, $sort, $limit, $skip);

        return $receiptOrderItems;
    }

    /**
     * @param string $id
     * @return ReceiptOrderItem
     */
    public function one(string $id) : ReceiptOrderItem
    {
        $receiptOrderItem = parent::findOneBy(['id'=>$id]);

        if($receiptOrderItem == null){
            throw new NotFoundHttpException("Элемент {$id} не найден");
        }

        return $receiptOrderItem;
    }


    /**
     * @param ReceiptOrderItem $receiptOrderItem
     * @return bool
     */
    public function delete(ReceiptOrderItem $receiptOrderItem) : bool
    {
        $this->dm->remove($receiptOrderItem);

        return true;
    }

}