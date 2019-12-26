<?php


namespace App\Controller;


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
     * ProductController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @Route("/order/make", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function makeForm(Request $request){
        $user = $this->getUser();
        return $this->render('order/make.html.twig', [
            'user'=>$user
        ]);
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