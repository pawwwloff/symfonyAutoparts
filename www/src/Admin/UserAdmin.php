<?php


namespace App\Admin;

use App\Document\PersonalAccount;
use App\Document\Supplier;
use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\UserBundle\Form\Type\SecurityRolesType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormTypeInterface;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = (!$this->getSubject() || null === $this->getSubject()->getId()) ? 'Registration' : 'Profile';

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        // avoid security field to be exported
        return array_filter(parent::getExportFields(), static function ($v) {
            return !\in_array($v, ['password', 'salt'], true);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user): void
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    public function setUserManager(UserManagerInterface $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('username', null, array('label' => 'Логин'))
            ->add('email', null, array('label' => 'Email'))
            //->add('groups', null, array('label' => 'Email'))
            ->add('enabled', null, ['editable' => true,'label' => 'Активность'])
            ->add('createdAt', null, array('label' => 'Дата создания'))
        ;

        /*if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', ['template' => '@SonataUser/Admin/Field/impersonating.html.twig'])
            ;
        }*/
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        $filterMapper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('groups')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->with('General')
            ->add('username')
            ->add('email')
            ->end()
            ->with('Groups')
            ->add('groups')
            ->end()
            ->with('Profile')
            ->add('dateOfBirth')
            ->add('firstname')
            ->add('lastname')
            ->add('website')
            ->add('biography')
            ->add('gender')
            ->add('locale')
            ->add('timezone')
            ->add('phone')
            ->end()
            ->with('Social')
            ->add('facebookUid')
            ->add('facebookName')
            ->add('twitterUid')
            ->add('twitterName')
            ->add('gplusUid')
            ->add('gplusName')
            ->end()
            ->with('Security')
            ->add('token')
            ->add('twoStepVerificationCode')
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        // define group zoning
        $formMapper
            ->tab('User', ['label' => 'Пользователь'])
                ->with('General', ['class' => 'col-md-6', 'label' => 'Основные'])->end()
                ->with('Roles', ['class' => 'col-md-6','label' => 'Роли'])->end()
            ->end()
        ;
        $formMapper
            ->tab('User')
                ->with('General')
                    ->add('username', null, ['label' => 'Логин (тел.)'])
                    ->add('email', null, ['label' => 'Email'])
                    ->add('plainPassword', TextType::class, [
                        'label' => 'Пароль',
                        'required' => (!$this->getSubject() || null === $this->getSubject()->getId()),
                    ])
                    ->add('firstname', null, ['required' => false,'label' => 'Имя'])
                    ->add('lastname', null, ['required' => false,'label' => 'Фамилия'])
                    ->add('phone', null, ['required' => false,'label' => 'Номер телефона'])
                    ->add('personalAccount', ModelType::class, [
                        'class'   => PersonalAccount::class,
                        'label' => 'Персональный счет',
                        'property' => 'name',
                        //'class' => Supplier::class,
                        //'choice_label' => 'name',
                    ])
                    ->add('enabled', null, ['required' => false,'label' => 'Активность'])
                ->end()
                ->with('Roles')
                    ->add('realRoles', SecurityRolesType::class, [
                        'label' => 'Роли',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ])
                ->end()
            ->end()
        ;
    }
}