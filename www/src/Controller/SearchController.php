<?php


namespace App\Controller;


use App\Service\OrderItemService;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var OrderItemService
     */
    private $orderItemService;

    /**
     * SearchController constructor.
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService, OrderItemService $orderItemService)
    {
        $this->productService = $productService;
        $this->orderItemService = $orderItemService;
    }

    /**
     * @Route("/search", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function search(Request $request){
        $query = $request->query->get('q');
        $user = $this->getUser();
        $cart = null;
        $products = null;
        if($query){
            $products = $this->productService->search($query);
            $cart = $this->orderItemService->getCartArray($user);
            foreach ($products as &$product){
                $quantity = 0;
                if($cart[$product->getId()]>0){
                    $quantity = $cart[$product->getId()];
                }
                $product->setQuantity($quantity);
            }
        }
        return $this->render('search/index.html.twig', [
            'products'=>$products
        ]);
    }
}