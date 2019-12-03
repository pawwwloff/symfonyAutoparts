<?php


namespace App\Controller\API;


use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;


    /**
     * ProductController constructor.
     * @param ProductService $productService
     */
    public function __construct(ProductService $productService)
    {

        $this->productService = $productService;
    }

    /**
     * @Route("/product/create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $product = $this->productService->add(
            $data['name'],
            $data['article'],
            $data['vendor'],
            $data['price'],
            $data['count'],
            $data['supplierId']
        );

        return $this->json($product);
    }

    /**
     * @Route("/product", methods={"GET"})
     * @return JsonResponse
     */
    public function list() : JsonResponse
    {
        $productrs = $this->productService->list();

        return $this->json($productrs);
    }
}