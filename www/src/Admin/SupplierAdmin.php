<?php


namespace App\Admin;

use App\Document\PersonalAccountLog;
use App\Repository\PersonalAccountLogRepository;
use App\Service\PersonalAccountLogService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class SupplierAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'supplier';
    protected $baseRoutePattern = 'supplier';

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('file', $this->getRouterIdParameter().'/file');
        $collection->add('download', $this->getRouterIdParameter().'/file_download');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class)
            ->add('email', EmailType::class);
            //->add('file', FileType::class, ['required'=>false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
            ->add('email');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('email', EmailType::class)
            ->add('_action', 'actions', [
                'lable'=>'Действия',
                'actions' => [
                    'file' => [
                        'template' => 'CRUD/supplier/list__action_download_file.html.twig',
                    ]
                ]
            ]);
    }
}