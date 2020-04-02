<?php


namespace App\Admin;

use App\Document\PersonalAccountLog;
use App\Repository\PersonalAccountLogRepository;
use App\Service\PersonalAccountLogService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PersonalAccountAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'account';
    protected $baseRoutePattern = 'account';
    /**
     * @var PersonalAccountLogService
     */
    private $logService;
    private $user;
    private $originalObject;
    /**
     * @var PersonalAccountLog
     */
    private $log;


    public function postPersist($object)
    {
        $description = $this->getForm()->get('description')->getData();
        $dm = $this->getModelManager()->getDocumentManager($this->getClass());
        $this->originalObject = $dm->getUnitOfWork()->getOriginalDocumentData($object);
        $dml = $this->getModelManager()->getDocumentManager(PersonalAccountLog::class);
        $palr = new PersonalAccountLogRepository($dml);
        $this->logService = new PersonalAccountLogService($palr);
        $this->user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $this->log = $this->logService->initNewLog($object, $this->user, 0, 0);
        $object->setAvailable();
        $this->logService->logManual($object,$this->log, $description);
    }


    public function preUpdate($object)
    {
        $dm = $this->getModelManager()->getDocumentManager($this->getClass());
        $this->originalObject = $dm->getUnitOfWork()->getOriginalDocumentData($object);
        $dml = $this->getModelManager()->getDocumentManager(PersonalAccountLog::class);
        $palr = new PersonalAccountLogRepository($dml);
        $this->logService = new PersonalAccountLogService($palr);
        $this->user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $this->log = $this->logService->initNewLog($object, $this->user, $this->originalObject['overdraft'], $this->originalObject['finance']);
        $object->setAvailable();
        parent::preUpdate($object);
    }

    public function postUpdate($object)
    {
        $description = $this->getForm()->get('description')->getData();
        $this->logService->logManual($object,$this->log, $description);
        parent::postUpdate($object);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class)
            ->add('overdraft', NumberType::class)
            ->add('finance', NumberType::class)
            ->add('description', TextType::class, ['mapped' => false]);
            /*->add('available', null,[
                'editable' => false
            ]);*/
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('overdraft')
            ->add('finance')
            ->add('available');
    }
}