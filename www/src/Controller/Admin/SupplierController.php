<?php

namespace App\Controller\Admin;

use App\Document\Product;
use App\Document\Supplier;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use App\Service\ProductService;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Service\SupplierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SupplierController extends AbstractController
{
    /**
     * @var SupplierService
     */
    private $supplierService;
    /**
     * @var SupplierRepository
     */
    private $supplierRepository;
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * SearchController constructor.
     * @param SupplierService $supplierService
     * @param SupplierRepository $supplierRepository
     * @param ProductService $productService
     * @param ProductRepository $productRepository
     */
    public function __construct(SupplierService $supplierService, SupplierRepository $supplierRepository,
                                ProductService $productService, ProductRepository $productRepository)
    {
        $this->supplierService = $supplierService;
        $this->supplierRepository = $supplierRepository;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
    }
    /**
     * @Route("/supplier", name="supplier_list", methods={"GET"})
     */
    public function index(Request $request)
    {
        $suppliers = $this->supplierService->list();
        return $this->render('admin/supplier/index.html.twig', [
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * @Route("/supplier/{id}/products", name="supplier_products", methods={"GET"})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function products(Request $request, $id)
    {
        $products = $this->productService->list(['supplier.id'=>$id]);
        return $this->render('admin/supplier/products.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/supplier/{id}/product/add", name="supplier_products_add")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productAdd(Request $request, $id)
    {
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('article', TextType::class)
            ->add('vendor', TextType::class)
            ->add('price', NumberType::class)
            ->add('count', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Добавить'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            $supplier = $this->supplierRepository->one($id);

            if($supplier) {
                $product->setSupplier($supplier);
                $product = $this->productRepository->save($product);
            }

            return $this->redirectToRoute('supplier_products', ['id'=>$id]);
        }

        return $this->render('admin/supplier/product.add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/supplier/create", name="supplier_add")
     */
    public function create(Request $request)
    {
        $supplier = new Supplier();
        $form = $this->createFormBuilder($supplier)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('emailForPrice', EmailType::class)
            ->add('emailTheme', TextType::class)
            ->add('searchFromXls', CheckboxType::class, array(
                'label'    => 'Искать по xls?',
                'required' => false))
            ->add('markup', IntegerType::class)
            ->add('deliveryTime', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Добавить'))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $supplier = $form->getData();
            $supplier = $this->supplierRepository->save($supplier);

            return $this->redirectToRoute('supplier_list');
        }

        return $this->render('admin/supplier/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
