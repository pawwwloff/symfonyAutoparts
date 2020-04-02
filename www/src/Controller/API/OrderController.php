<?php


namespace App\Controller\API;


use App\Document\Order;
use App\Form\OrderMakeType;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    private $orderService;


    /**
     * ProductController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @Route("/order/make", methods={"POST", "GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function make(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderMakeType::class, $order);
        $form->handleRequest($request);
        if($request->getMethod()=='POST'){
            if (!$form->isValid()) {
                dd($form->getErrors(true, false));
            }
        }
        $order = [];
        /*if($user) {
            $order = $this->orderService->add(
                $data['fio'],
                $data['email'],
                $data['phone'],
                $user
            );
        }*/

        return $this->json($order);
    }

    /**
     * @Route("/order", methods={"GET"})
     * @return JsonResponse
     */
    public function list(Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $orders = null;
        if($user){
            $orders = $this->orderItemService->getForUser($user);
        }

        return $this->json($orders);
    }
}