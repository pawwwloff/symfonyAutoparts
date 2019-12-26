<?php


namespace App\Controller;


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
     * @Route("/cart", methods={"GET"})
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