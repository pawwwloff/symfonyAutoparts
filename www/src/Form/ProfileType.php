<?php


namespace App\Form;


use App\Document\User;
use App\Form\Type\SpanType;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use FOS\UserBundle\Form\Type\ProfileFormType as AbstractType;
//use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function __construct()
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['data'];
        $types = array_flip(User::$userTypes);
        $type = $user->getType();

        /** TODO эти поля вывести через вьюху*/
        /*$builder->add('type', SpanType::class, [
            'label'=>"Тип пользователя",
            'data'=>['value'=>$types[$type]],
            'required' => false
        ]);

        $builder->add('phone', SpanType::class, [
            'label'=>"form.phone",
            'data'=>['value'=>$user->getPhone()],
            'required' => false
        ]);*/
        $builder->add('email', EmailType::class, [
            'label' => 'form.email',
            'translation_domain' => 'FOSUserBundle'
        ]);
        if ($type == User::USER_TYPE_ENTITY) {
            $builder->add('company', TextType::class, [
                'attr' => ['data-type' => User::USER_TYPE_ENTITY],
                'translation_domain' => 'FOSUserBundle'
            ]);
            $builder->add('inn', TextType::class, [
                'attr' => ['data-type' => User::USER_TYPE_ENTITY],
                'translation_domain' => 'FOSUserBundle'
            ]);
            $builder->add('kpp', TextType::class, [
                'attr' => ['data-type' => User::USER_TYPE_ENTITY],
                'translation_domain' => 'FOSUserBundle'

            ]);
            $builder->add('city', TextType::class, [
                'attr' => ['data-type' => User::USER_TYPE_ENTITY],
                'translation_domain' => 'FOSUserBundle'
            ]);
        } else {
            $builder->add('first_name', SpanType::class, [
                'label'=>"ФИО",
                'data'=>['value'=>$user->getFullName()],
            ]);
        }
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array(
                'translation_domain' => 'FOSUserBundle',
                'attr' => array(
                    'autocomplete' => 'new-password',
                ),
            ),
            'first_options' => array('label' => 'form.password'),
            'second_options' => array('label' => 'form.password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
            'required' => false
        ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_token_id' => 'profile',
        ));
    }

    public function getBlockPrefix()
    {
        return 'fos_user_profile';
    }

}