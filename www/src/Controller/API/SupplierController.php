<?php


namespace App\Controller\API;


use App\Service\SupplierService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SupplierController extends Controller
{
    /**
     * @var SupplierService
     */
    private $supplierService;

    /**
     * SupplierController constructor.
     */
    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * @Route("/supplier", methods={"GET"})
     */
    public function list() : JsonResponse
    {
        $suppliers = $this->supplierService->list();
        return $this->json($suppliers);
    }

    /**
     * @Route("/supplier/create", methods={"POST"})
     */
    public function create(Request $request) : JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->supplierService->create(
            $data['name'],
            $data['email'],
            $data['emailForPrice'] ?? '',
            $data['emailTheme'] ?? '',
            $data['status'] ?? '',
            $data['searchFromXls'] ?? '',
            $data['markup'] ?? 10,
            $data['deliveryTime'] ?? 5,
            $data['file'] ?? ''
        );

        return $this->json($data);
    }

    public function remove(){

    }

    public function edit(){

    }
}