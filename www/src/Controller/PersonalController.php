<?php

namespace App\Controller;

use App\Service\OrderItemService;
use App\Service\PersonalAccountLogService;
use App\Service\PersonalAccountService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonalController extends AbstractController
{
    /**
     * @var OrderItemService
     */
    private $orderItemService;
    /**
     * @var PersonalAccountService
     */
    private $personalAccountService;
    /**
     * @var PersonalAccountLogService
     */
    private $personalAccountLogService;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @param OrderItemService $orderItemService
     * @param PersonalAccountService $personalAccountService
     * @param PersonalAccountLogService $personalAccountLogService
     * @param PaginatorInterface $paginator
     */
    public function __construct(OrderItemService $orderItemService,
                                PersonalAccountService $personalAccountService,
                                PersonalAccountLogService $personalAccountLogService,
                                PaginatorInterface $paginator)
    {
        $this->orderItemService = $orderItemService;
        $this->personalAccountService = $personalAccountService;
        $this->personalAccountLogService = $personalAccountLogService;
        $this->paginator = $paginator;
    }
    /**
     * @Route("/personal", name="personal")
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $orders = null;
        if($user){
            $orders = $this->orderItemService->getForUser($user, true);
        }

        return $this->render('personal/index.html.twig', [
            'user'=>$user,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/personal/balance", name="personal_balance")
     */
    public function personalBalance(Request $request)
    {
        $user = $this->getUser();
        $logs = null;
        if($user && $user->getPersonalAccount()){
            $logs = $this->personalAccountLogService->getByUserAccount($user->getPersonalAccount(),$request->query->getInt('page', 1));
        }

        return $this->render('personal/balance.html.twig', [
            'user'=>$user,
            'logs' => $logs,
            'pagination' => $logs,
        ]);
    }

    /**
     * @Route("/personal/pay", methods={"POST"})
     * * @param Request $request
     * @return JsonResponse
     */
    public function pay(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $orderId = $data['id'];
        $user = $this->getUser();
        $order = null;
        $personalAccount = null;
        $result = [];
        if($user){
            $personalAccount = $user->getPersonalAccount();
            $order = $this->orderItemService->getByIdAndUser($user, $orderId);
            if($order){
                $result = $this->orderItemService->paid($order, $personalAccount);
                if($result) {
                    $this->personalAccountService->paid($order, $personalAccount, $user);
                }
            }
            $result = ['order'=>$order, 'account'=>$personalAccount];
        }
        return $this->json($result);
    }

}
