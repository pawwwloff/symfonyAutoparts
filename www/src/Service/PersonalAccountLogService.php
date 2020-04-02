<?php


namespace App\Service;


use App\Document\OrderItem;
use App\Document\PersonalAccount;
use App\Document\PersonalAccountLog;
use App\Repository\PersonalAccountLogRepository;
use Knp\Component\Pager\PaginatorInterface;


class PersonalAccountLogService
{

    /**
     * @var PersonalAccountLogRepository
     */
    private $personalAccountLogRepository;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * ProductService constructor.
     * @param PersonalAccountLogRepository $personalAccountLogRepository
     */
    public function __construct(PersonalAccountLogRepository $personalAccountLogRepository, PaginatorInterface $paginator)
    {
        $this->personalAccountLogRepository = $personalAccountLogRepository;
        $this->paginator = $paginator;
    }

    public function getByUserAccount(PersonalAccount $personalAccount, $page = 1)
    {
        $paQuery = $this->personalAccountLogRepository->createQueryBuilder()
            ->field('personalAccount')->equals($personalAccount)
            ->getQuery();
        $pagination = $this->paginator->paginate(
            $paQuery, /* query NOT result */
            $page, /*page number*/
            10 /*limit per page*/
        );
        return $pagination;
        /*$logs = $this->personalAccountLogRepository->list(['personalAccount'=>$personalAccount]);
        return $logs;*/
    }

    public function initNewLog(PersonalAccount $personalAccount, $user, $overdraft = 0, $finance = 0){
        $log = new PersonalAccountLog();
        $log->setUser($user);
        $log->setPersonalAccount($personalAccount);
        $log->setFinance($finance);
        $log->setOverdraft($overdraft);
        $log->setAvailable();

        return $log;
    }

    public function logManual(PersonalAccount $personalAccount, PersonalAccountLog $log,
                              $description = 'Ручное изменение счета'){
        $log->setOperation(PersonalAccountLog::MANUAL);
        $log->setNewFinance($personalAccount->getFinance());
        $log->setNewOverdraft($personalAccount->getOverdraft());
        $log->setNewAvailable();
        $log->setDescription($description);
        $log = $this->personalAccountLogRepository->save($log);
        return $log;
    }

    public function logChangePrice(PersonalAccount $personalAccount, PersonalAccountLog $log,
                                   OrderItem $orderItem, $description = 'Изменение суммы заказа'){
        $log->setOrderNumber($orderItem->getOrder()->getId() . '/' . $orderItem->getNumber());
        $log->setOperation(PersonalAccountLog::MANUAL);
        $log->setNewFinance($personalAccount->getFinance());
        $log->setNewOverdraft($personalAccount->getOverdraft());
        $log->setNewAvailable();
        $log->setDescription($description);
        $log = $this->personalAccountLogRepository->save($log);
        return $log;
    }

    public function logChangeStatus(PersonalAccount $personalAccount, PersonalAccountLog $log,
                                    OrderItem $orderItem, $oldStatus, $description){
        $newStatus = $orderItem->getStatusName();
        $oldStatus = $orderItem->getStatuses()[$oldStatus];
        $log->setOrderNumber($orderItem->getOrder()->getId() . '/' . $orderItem->getNumber());
        $log->setOperation(PersonalAccountLog::MANUAL);
        $log->setNewFinance($personalAccount->getFinance());
        $log->setNewOverdraft($personalAccount->getOverdraft());
        $log->setNewAvailable();
        $log->setDescription("$description с '$oldStatus' на '$newStatus'");
        $log = $this->personalAccountLogRepository->save($log);
        return $log;
    }

    public function logPaid(PersonalAccount $personalAccount, PersonalAccountLog $log, OrderItem $orderItem){
        $log->setOrderNumber($orderItem->getOrder()->getId() . '/' . $orderItem->getNumber());
        $log->setOperation(PersonalAccountLog::PAID);
        $log->setNewFinance($personalAccount->getFinance());
        $log->setNewOverdraft($personalAccount->getOverdraft());
        $log->setNewAvailable();
        $log->setDescription('Оплата заказа');
        $this->personalAccountLogRepository->save($log);
        return $log;
    }

}