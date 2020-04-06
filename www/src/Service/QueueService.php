<?php


namespace App\Service;


use App\Document\Product;
use Doctrine\ODM\MongoDB\DocumentManager;
use Pheanstalk\Job;
use Pheanstalk\Pheanstalk;

class QueueService
{
    private $queue;
    /**
     * @var Pheanstalk
     */
    private $client;
    /**
     * @var FilesService
     */
    private $filesService;
    /**
     * @var DocumentManager
     */
    private $dm;
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(FilesService $filesService,
                                ProductService $productService,
                                DocumentManager $dm)
    {
        $this->queue = 'autoparts';
        $this->client = Pheanstalk::create('beanstalkd');
        //$this->client = new Pheanstalk('beanstalkd');
        $this->filesService = $filesService;
        $this->dm = $dm;
        $this->productService = $productService;
    }

    public function send($request)
    {
        return $this->client
            ->useTube($this->queue)
            ->put(json_encode($request)); // Отправьте что угодно с кодировкой в формате json – и готово!
    }

    public function listen()
    {
        $this->client->watch($this->queue); // Снова передайте имя очереди.
        $time = time();
        while ($job = $this->client->reserve()) { // Продолжайте это делать... чтобы он всегда слушал.
            $result = $this->process($job);
             echo time()-$time;
            if ($result['status'] == 'delete')
                $this->client->delete($job);
            elseif($result['status'] == 'release') {
                $this->client->delete($job);
                //$this->client->release($result['job'], 2);
                $this->client->useTube($this->queue)
                    ->put(json_encode($result['message']), 0);
            }
        }

    }

    public function process($job)
    {
        $count = 1;
        $message = json_decode($job->getData(), true);
        if(!isset($message['skip']))$message['skip'] = 0;
        $file = $this->filesService->getById($message['file_id']);
        if(!file_exists($file->getFile())){
            return ['status' => 'delete'];
        }
        $supplier = $file->getSupplier();
        if(!$supplier){
            /** TODO тут выкинуть исключение, ну либо что то придумать */
            die();
        }
        $lines = ReadCsvService::getLines($file->getFile(), $count, $message['skip']);
        $settings = $file->getJsonSettings();
        if(count($lines['lines'])<=0){
            unlink($file->getFile());
            return ['status' => 'delete'];
        }else{
            if($message['skip']==0){
                /** Удаляем все элементы этого поставщика */
                $this->dm->createQueryBuilder(Product::class)
                    ->remove()
                    ->field('supplier')->equals($supplier)
                    ->getQuery()
                    ->execute();
            }
            $arFields = self::setFields($lines['lines'],$settings);

            $this->productService->addArray($arFields,$supplier->getId());
            if(isset($lines['end']) && $lines['end']==true){
                unlink($file->getFile());
                return ['status' => 'delete'];
            }

        }
        $message['skip']=$count+$message['skip'];

        return ['status' => 'release', 'message'=>$message];
    }

    public static function setFields($lines, $settings){
        $arFields = [];
        foreach ($lines as $line){
            $arField = [];
            foreach ($settings as $key=>$value){
                $arField[$value] = $line[$key];
            }
            if($arField){
                $arFields[] = $arField;
            }
        }

        return $arFields;
    }
}