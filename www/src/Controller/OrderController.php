<?php


namespace App\Controller;


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
     * @Route("/cart/add", methods={"POST", "GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $session = $request->getSession();
        $session->start();
        $sessionId = $session->getId();

        if($sessionId) {
            $order = $this->orderService->addInCart(
                $data['product'],
                $data['quantity'],
                $user,
                $sessionId
            );
        }

        return $this->json($order);
    }

    /**
     * @Route("/cart", methods={"GET"})
     * @return JsonResponse
     */
    public function list(Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $session = $request->getSession();
        $session->start();
        $sessionId = $session->getId();
        $orders = null;
        if($user){
            $orders = $this->orderService->getForUser($user);
        }elseif ($sessionId){
            $orders = $this->orderService->getForSession($sessionId);
        }

        return $this->json($orders);
    }
}