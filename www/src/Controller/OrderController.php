<?php


namespace App\Controller;


use App\Document\Order;
use App\Form\OrderMakeType;
use App\Service\OrderItemService;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends Controller
{
    /**
     * @var OrderService
     */
    private $orderService;
    /**
     * @var OrderItemService
     */
    private $orderItemService;


    /**
     * ProductController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService, OrderItemService $orderItemService)
    {
        $this->orderService = $orderService;
        $this->orderItemService = $orderItemService;
    }

    /**
     * @Route("/order", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function order(Request $request){
        /** TODO добавить способы доставки */
        $user = $this->getUser();
        $order = new Order();
        $orders = [];
        $form = $this->createForm(OrderMakeType::class, $order);
        if($user){
            $orders = $this->orderItemService->getForUser($user);
        }
        return $this->render('order/make.html.twig', [
            'user'=>$user,
            'form' => $form->createView(),
            'products' => $orders
        ]);
    }

    /**
     * @Route("/order/make", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function make(Request $request){
        $user = $this->getUser();
        $order = new Order();
        $form = $this->createForm(OrderMakeType::class, $order);
        $form->handleRequest($request);
        if($request->getMethod()=='POST'){
            if (!$form->isValid()) {
                if($user){
                    $orders = $this->orderItemService->getForUser($user);
                }
                return $this->json($this->render('order/_make_content.html.twig', [
                    'user'=>$user,
                    'form' => $form->createView(),
                    'products' => $orders
                ]));
                //dd($form->getErrors(true, false));
            }else{
                if($user) {
                    $data = $form->getData();
                    $order = $this->orderService->add(
                        $data->getFio(),
                        $data->getEmail(),
                        $data->getPhone(),
                        $user
                    );
                    if($order) {
                        return $this->json($this->render('order/_success_content.html.twig', [
                            'user' => $user,
                            'order' => $order
                        ]));
                    }
                }
            }
        }

    }

    /**
     * @Route("/cart", methods={"GET"})
     * @return Response
     */
    public function list(Request $request) : Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $orders = null;
        if($user){
            $orders = $this->orderItemService->getForUser($user);
        }

        return $this->render('order/cart.html.twig', [
            'user'=>$user,
            'products' => $orders
        ]);

        //return $this->json($orders);
    }
}