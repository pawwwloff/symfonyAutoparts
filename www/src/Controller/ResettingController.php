<?php


namespace App\Controller;

use App\Form\CheckCodeType;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ResettingController extends BaseController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenGenerator;
    private $mailer;

    /**
     * @var int
     */
    private $retryTtl;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param FactoryInterface         $formFactory
     * @param UserManagerInterface     $userManager
     * @param TokenGeneratorInterface  $tokenGenerator
     * @param MailerInterface          $mailer
     * @param int                      $retryTtl
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer, $retryTtl)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->retryTtl = $retryTtl;
    }


    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');
        if(!$username){
            new RedirectResponse($this->generateUrl('fos_user_resetting_request', array('username' => $username)));
        }
        $user = $this->userManager->findUserByEmail($username);
        if (!$user) {
            $user = $this->userManager->findUserBy(array('phone' => $username));
        }

        $event = new GetResponseNullableUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        if (null !== $user) {
            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $user->setConfirmationToken(rand(1000,9999));

            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
            $user->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($user);
            /** Установим в сессию эти два поля для следующего шага*/
            $request->getSession()->set('fos_user_send_confirmation_email/email', $user->getEmail());
            $request->getSession()->set('registration_phone', $user->getPhone());
            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);
/** TODO тут отсылаем код на телефон (возможно есть смысл захерачить это в эвент что бы было все в одном месте) */
            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
            return new RedirectResponse($this->generateUrl('check_resetting'));
        }

        return new RedirectResponse($this->generateUrl('fos_user_resetting_request', array('username' => $username)));
    }

    public function checkAction(Request $request)
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
                $token = $this->tokenGenerator->generateToken();
                $user->setConfirmationToken($token);
                $this->userManager->updateUser($user);
                $url = $this->generateUrl('fos_user_resetting_reset', ['token'=>$token]);
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
}