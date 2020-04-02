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
     * @Route("/cart/add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request) : JsonResponse
    {
        $data = $request->request->all();
        $user = $this->getUser();
        $orders = null;
        if($user) {
            $order = $this->orderItemService->addInCart(
                $data['product'],
                $data['quantity'],
                $user
            );

            $cart = $this->orderItemService->getCart($user);
        }

        return $this->json($cart);
    }
}