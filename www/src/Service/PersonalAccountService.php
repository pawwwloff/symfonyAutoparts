<?php


namespace App\Service;


use App\Document\OrderItem;
use App\Document\PersonalAccount;
use App\Document\PersonalAccountLog;
use App\Document\User;
use App\Repository\PersonalAccountLogRepository;
use App\Repository\PersonalAccountRepository;


class PersonalAccountService
{
    /**
     * @var PersonalAccountRepository
     */
    private $personalAccountRepository;

    /**
     * @var PersonalAccountLogService
     */
    private $personalAccountLogService;


    /**
     * ProductService constructor.
     * @param PersonalAccountRepository $personalAccountRepository
     * @param PersonalAccountLogService $personalAccountLogService
     */
    public function __construct(PersonalAccountRepository $personalAccountRepository,
                                PersonalAccountLogService $personalAccountLogService)
    {
        $this->personalAccountRepository = $personalAccountRepository;
        $this->personalAccountLogService = $personalAccountLogService;
    }

    public function getByUser(User $user){
        return $this->personalAccountRepository->list(['user'=>$user]);
    }


    public function update(PersonalAccount $personalAccount){
        $personalAccount->setAvailable();
        $this->personalAccountRepository->save($personalAccount);
        return $personalAccount;
    }

    public function paid(OrderItem $order,PersonalAccount $personalAccount, $user){
        $sum = $order->getSumm();
        $finance = $personalAccount->getFinance();
        $overdraft = $personalAccount->getOverdraft();
        $available = $finance+$overdraft;
        if($available>=$sum){
            $log = $this->personalAccountLogService->initNewLog($personalAccount, $user, $overdraft, $finance); // Тут инициируем лог
            $personalAccount->setFinance($finance - $sum);
            $personalAccount = $this->update($personalAccount);
            $this->personalAccountLogService->logPaid($personalAccount,$log, $order);
        }
    }

    public function changeStatus(OrderItem $order,PersonalAccount $personalAccount, $user, $oldStatus, $operation){
        $sum = $order->getSumm();
        $finance = $personalAccount->getFinance();
        $overdraft = $personalAccount->getOverdraft();
        $log = $this->personalAccountLogService->initNewLog($personalAccount, $user, $overdraft, $finance); // Тут инициируем лог
        if($operation==OrderItem::OPERATION_RETURN){
            $personalAccount->setFinance($finance + $sum);
            $description = 'Пополнение счета из за изменения статуса заказа';
        }else{
            $personalAccount->setFinance($finance - $sum);
            $description = 'Списание средств из за изменения статуса заказа';
        }
        $personalAccount = $this->update($personalAccount);
        $this->personalAccountLogService->logChangeStatus($personalAccount, $log, $order, $oldStatus, $description);
    }

    public function changePrice(OrderItem $order,PersonalAccount $personalAccount, $user, $oldSum, $description){
        $sum = $order->getSumm();
        $finance = $personalAccount->getFinance();
        $overdraft = $personalAccount->getOverdraft();

        $log = $this->personalAccountLogService->initNewLog($personalAccount, $user, $overdraft, $finance); // Тут инициируем лог
        $personalAccount->setFinance($finance + $oldSum - $sum);
        $personalAccount = $this->update($personalAccount);
        $this->personalAccountLogService->logChangePrice($personalAccount, $log, $order, $description);
    }


}