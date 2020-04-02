<?php


namespace App\Service;


use App\Document\Supplier;
use App\Repository\SupplierRepository;

class SupplierService
{
    /**
     * @var SupplierRepository
     */
    private $repository;

    /**
     * SupplierService constructor.
     * @param SupplierRepository $repository
     */
    public function __construct(SupplierRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $emailForPrice
     * @param string $emailTheme
     * @param string $status
     * @param string $searchFromXls
     * @param int $markup
     * @param int $deliveryTime
     * @param string $file
     * @return Supplier
     */
    public function create(string $name, string $email = '', string $emailForPrice = '',
                           string $emailTheme = '', string $status = '', string $searchFromXls = '',
                           int $markup = 10, int $deliveryTime = 5, string $file = '') : Supplier
    {

        $supplier = new Supplier($name);
        $supplier->setEmail($email);
        $supplier->setEmailForPrice($emailForPrice);
        $supplier->setEmailTheme($emailTheme);
        $supplier->setStatus($status);
        $supplier->setSearchFromXls($searchFromXls);
        $supplier->setMarkup($markup);
        $supplier->setDeliveryTime($deliveryTime);
        $supplier->setFile($file);

        $this->repository->save($supplier);

        return $supplier;

    }

    public function list() : array
    {
        return $this->repository->list();
    }

    public function getById($id) : Supplier
    {
        return $this->repository->one($id);
    }
}