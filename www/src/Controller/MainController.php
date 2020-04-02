<?php

namespace App\Controller;

use App\Service\OrderItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @var OrderItemService
     */
    private $orderItemService;

    /**
     * @param OrderItemService $orderItemService
     */
    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }
    /**
     * @Route("/", name="main")
     */
    public function index(Request $request)
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route(name="cartBlock")
     */
    public function cart(){
        $user = $this->getUser();
        $cart = ['summ'=>0];
        if($user) {
            $cart = $this->orderItemService->getCart($user);
        }
        return $this->render('_cart.html.twig', [
            'cart' => $cart,
        ]);
    }
}
