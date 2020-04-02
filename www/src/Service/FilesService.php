<?php


namespace App\Service;


use App\Document\Files;
use App\Document\Supplier;
use App\Repository\FilesRepository;


class FilesService
{
    /**
     * @var FilesRepository
     */
    private $repository;

    /**
     * SupplierService constructor.
     * @param FilesRepository $repository
     */
    public function __construct(FilesRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param $supplier
     * @return Files|null
     */
    public function getBySupplier($supplier) : ?Files
    {
        return $this->repository->findOneBy(['supplier'=>$supplier]);
    }

    /**
     * @param Supplier $supplier
     * @param $newFile
     * @param $jsonSettings
     * @return Files
     */
    public function add($newFile, $supplier, $jsonSettings) : Files
    {
        $newFile->setJsonSettings($jsonSettings);
        $newFile->setSupplier($supplier);
        $newFile = $this->repository->save($newFile);

        return $newFile;

    }

    public function update($files, $supplier, $jsonSettings) : Files
    {
        $files->setSupplier($supplier);
        $files->setJsonSettings($jsonSettings);
        $files = $this->repository->save($files);


        return $files;

    }
}