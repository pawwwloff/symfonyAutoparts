<?php


namespace App\Controller;


use App\Form\CheckCodeType;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends BaseController
{

    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function checkPhoneAction(Request $request)
    {
        $user = null;
        if(!$field = $request->request->get('check_code')['field']) {
            $field = $request->getSession()->get('fos_user_send_confirmation_email/email');
        }
        if ($field) {
            $user = $this->userManager->findUserByEmail($field);
        }
        if (!$user) {
            $field = $request->getSession()->get('registration_phone');
            if ($field) {
                $user = $this->userManager->findUserBy(array('phone' => $field));
            }
        }

        $form = $this->createForm(CheckCodeType::class);
        $form->setData(['field'=>$field]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $data = $form->getData();
            if($user && $user->getConfirmationToken()==$data['code']){
                $user->setConfirmationToken(null);
                $user->setEnabled(true);
                $this->userManager->updateUser($user);
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
                $request->getSession()->remove('registration_phone');
                $request->getSession()->remove('fos_user_send_confirmation_email/email');
                return $response;
            }else{
                $form->addError(new FormError('Неверные поля'));
            }
        }

        return $this->render('@FOSUser/Registration/check_phone.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function registerAction(Request $request)
    {

        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);

                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $this->userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render('@FOSUser/Registration/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}