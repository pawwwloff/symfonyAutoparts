<?php


namespace App\Repository;


use App\Document\OrderItem;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderItemRepository extends DocumentRepository
{

    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(OrderItem::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param array $orders
     * @return OrderItem[]
     */
    public function saveArray(array $orderItems) : array
    {
        foreach ($orderItems as $orderItem){
            $this->dm->persist($orderItem);
        }
        $this->dm->flush();

        return $orderItems;
    }

    /**
     * @param array $criteria
     * @param array|null $sort
     * @param int $limit
     * @param null $skip
     * @return OrderItem[]
     */
    public function list(array $criteria = [], array $sort=null, $limit=10, $skip=null) : array
    {
        $orderItems = parent::findBy($criteria, $sort, $limit, $skip);

        return $orderItems;
    }

    /**
     * @param mixed $id
     * @return OrderItem
     */
    public function one($id) : OrderItem
    {
        $orderItem = parent::findOneBy(['id'=>$id]);

        if($orderItem == null){
            throw new NotFoundHttpException("Заказ {$id} не найден");
        }

        return $orderItem;
    }


    /**
     * @param OrderItem $order
     * @return bool
     */
    public function delete(OrderItem $orderItem) : bool
    {

        $this->dm->remove($orderItem);
        $this->dm->flush();
        return true;
    }

    /**
     * @param OrderItem $order
     * @return OrderItem
     */
    public function save(OrderItem $orderItem) : OrderItem
    {
        $this->dm->persist($orderItem);
        $this->dm->flush();

        return $orderItem;
    }
}