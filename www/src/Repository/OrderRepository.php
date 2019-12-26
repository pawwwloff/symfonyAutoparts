<?php


namespace App\Repository;


use App\Document\Order;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderRepository extends DocumentRepository
{

    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(Order::class);
        parent::__construct($dm, $uow, $classMetaData);
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
        $orders = parent::findBy($criteria, $sort, $limit, $skip);

        return $orders;
    }

    /**
     * @param int $id
     * @return OrderItem
     */
    public function one(int $id) : OrderItem
    {
        $order = parent::findOneBy(['id'=>$id]);

        if($order == null){
            throw new NotFoundHttpException("Заказ {$id} не найден");
        }

        return $order;
    }


    /**
     * @param Order $order
     * @return bool
     */
    public function delete(Order $order) : bool
    {
        $this->dm->remove($order);
        $this->dm->flush();
        return true;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function save(Order $order) : Order
    {
        $this->dm->persist($order);
        $this->dm->flush();

        return $order;
    }
}