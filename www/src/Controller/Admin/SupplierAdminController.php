<?php

namespace App\Controller\Admin;

use App\Document\Files;
use App\Document\Product;
use App\Document\Supplier;
use App\Form\FilesType;
use App\Form\FileType;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use App\Service\FilesService;
use App\Service\ProductService;
use App\Service\QueueService;
use App\Service\ReadCsvService;
use Mnvx\Lowrapper\Converter;
use Mnvx\Lowrapper\Format;
use Mnvx\Lowrapper\LowrapperParameters;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Service\SupplierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SupplierAdminController extends CRUDController
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
     * @var FilesService
     */
    private $filesService;
    /**
     * @var QueueService
     */
    private $queueService;

    /**
     * SearchController constructor.
     * @param SupplierRepository $supplierRepository
     * @param ProductService $productService
     * @param ProductRepository $productRepository
     * @param FilesService $filesService
     * @param QueueService $queueService
     */
    public function __construct(SupplierRepository $supplierRepository,
                                ProductService $productService,
                                ProductRepository $productRepository,
                                FilesService $filesService,
                                QueueService $queueService)
    {
        $this->supplierRepository = $supplierRepository;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->filesService = $filesService;
        $this->queueService = $queueService;
    }

    /**
     * @Route("/supplier/{id}/products", name="admin_supplier_products", methods={"GET"})
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
     * @Route("/supplier/{id}/product/add", name="admin_supplier_products_add")
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

    public function downloadAction(Request $request){
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $object = $this->admin->getSubject();
        $fileName = $object->getId();
        $converter = new Converter();

        $parameters = (new LowrapperParameters())
            // HTML document
            ->setInputFile($file->getRealPath())
            // Format of result document is docx
            ->setOutputFormat(Format::SPREADSHEET_CSV)
            ->addOutputFilter('Text - txt - csv (StarCalc):59,34,76')
            // Result file name
                /** TODO вынести путь к файлу в настройки */
            ->setOutputFile("/var/www/html/public/files/file-{$fileName}.csv");

        $converter->convert($parameters);

        $lines = ReadCsvService::getLines($parameters->getOutputFile(), 5, 0);

        $return['filepath'] = $parameters->getOutputFile();
        $return['lines'] = json_encode($lines);
        $return['html'] = $this->render('CRUD/supplier/download_file_html_block.html.twig', [
            'lines'=>$lines,
            'select'=>Files::FIELD_SELECT,
            'settings'  => [],
        ])->getContent();
        return $this->json($return);

    }

    public function fileAction(Request $request)
    {
        /** TODO
         *  ГОТОВО - тут делаем форму загрузки файла, если файл уже загружен выводим таблицу
         *  ГОТОВО - Ajax загрузка файла с автоматическим переводом в csv
         *  ГОТОВО - создал - SupplierFiles в нем все что связанно с файлами
         *  ГОТОВО - Добавить кнопку сохранить, и сохранение элемента (видимо на этом же роуте)
         *  ГОТОВО - Создать воркер для разбора файла (пока что будем руками, в дальнейшем все на менеджера очередей переводим)
         *
         * Сделать выполнение воркера на supervisor (php bin/console app:download-csv)
         */
        $object = $this->admin->getSubject();
        if(!$object){
            /** TODO exception */
            return $this->redirectTo($object);
        }
        $file = $this->filesService->getBySupplier($object);
        if(!$file){
            $file = new Files();
        }

        $this->admin->checkAccess('edit');
        $form = $this->createForm(FilesType::class,$file);
        $form->handleRequest($request);
        if($request->getMethod()=='POST'){
            if ($form->isValid()) {
                $jsonSettings = $request->get('jsonSettings', []);
                $data = $form->getData();
                if(!$data->getId()) {
                    $file = $this->filesService->add($data, $object, $jsonSettings);
                }else{
                    $file = $this->filesService->update($data, $object, $jsonSettings);
                }
                if($file) {
                    $this->queueService->send([
                        'file_id'=>$file->getId(),
                        'skip'=>0
                    ]);
                    if($request->get('btn_update_and_list')!==NULL){
                        return $this->redirectTo($object);
                    }
                }
            }else{
                $this->addFlash('sonata_flash_error', 'Ошибка валидации формы');
            }
        }

        return $this->renderWithExtraParams('CRUD/supplier/download_file.html.twig', [
            'action' => 'list',
            'object' => $object,
            'file'   => $file,
            'lines'  => $file->getJsonTableArray(),
            'settings'  => $file->getJsonSettings(),
            'formFiles' => $form->createView(),
            'select'=>Files::FIELD_SELECT,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
        ], null);

    }


    private function getFoldersForUploadFile($type)
    {
        $fileType = $this->returnExistFileType($type); #метод возвращающюй тип файлов которые можно грузить

        if ($fileType !== null) {
            return array(
                'root_dir' => $this->container->getParameter('upload_' . $fileType . '_root_directory'), # полный путь к папке с картинкой
                'dir' => $this->container->getParameter('upload_' . $fileType . '_directory'), # отосительный путь к папке
            );
        } else {
            return null;
        }
    }

    # метод возвращает ключ(тип) файла который будет закачиваться
    private function returnExistFileType($type)
    {
        $typeArray = array(
            'img' => array(
                'image/png',
                'image/jpg',
                'image/jpeg',
            ),
            'pdf' => array(
                'application/pdf',
                'application/x-pdf',
            )
        );

        foreach ($typeArray as $key => $value) {
            if (in_array($type, $value)) {
                return $key;
            }
        }

        return null;
    }

    # Тут собственно все и происходит. Загрузка, присвоение имени, перемещение в папку
    private function upload($file)
    {
        $filePath = $this->getFoldersForUploadFile($file['type']);

        if (null === $this->getFileInfo($file['name']) || $filePath === null) {

            return null;
        }
        $pathInfo = $this->getFileInfo($file['name']);
        $path = $this->fileUniqueName() . '.' . $pathInfo['extension'];
        $this->uploadFileToFolder($file['tmp_name'], $path, $filePath['root_dir']);

        unset($file);
        return $filePath['dir'] . DIRECTORY_SEPARATOR . $path;
    }

    # возвращает всю информацию о загруженном фале (что бы это не было)
    private function getFileInfo($file)
    {

        return $file !== null ? (array)pathinfo($file) : null;
    }

    # формирует уникальное имя
    private function fileUniqueName()
    {

        return sha1(uniqid(mt_rand(), true));
    }

    # перемещает файл в необходимую папку
    private function uploadFileToFolder($tmpFile, $newFileName, $rootFolder)
    {
        $e = new File($tmpFile);
        $e->move($rootFolder, $newFileName);
    }
}
