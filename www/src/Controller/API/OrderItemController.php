<?php


namespace App\Controller\API;


use App\Service\OrderItemService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderItemController extends Controller
{
    /**
     * @var OrderItemService
     */
    private $orderItemService;


    /**
     * ProductController constructor.
     * @param OrderItemService $orderItemService
     */
    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
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

        if($user) {
            $order = $this->orderItemService->addInCart(
                $data['product'],
                $data['quantity'],
                $user
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
            $orders = $this->orderItemService->getForUser($user);
        }elseif ($sessionId){
            $orders = $this->orderItemService->getForSession($sessionId);
        }

        return $this->json($orders);
    }
}