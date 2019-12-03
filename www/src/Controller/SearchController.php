<?php


namespace App\Controller;


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
     * SearchController constructor.
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/search", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function search(Request $request){
        $query = $request->query->get('q');
        $products = [];
        if($query){
            $products = $this->productService->search($query);
        }
        return $this->render('search/index.html.twig', [
            'products'=>$products
        ]);
    }
}