<?php


namespace App\EventListener;


use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class UserRegistrationListener implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => ['onRegistrationSuccess', -1],
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    public function onRegistrationSuccess(FormEvent  $event)
    {
        $user = $event->getForm()->getData();
        $user->setConfirmationToken(rand(1000,9999));
        $event->getRequest()->getSession()->set('registration_phone', $user->getPhone());
        $url = $this->router->generate('check_phone');
        $event->setResponse(new RedirectResponse($url));
    }
    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        /** TODO тут будет отправка смс на номер телефона с кодом регистрации*/
        $user = $event->getForm()->getData();
        $phone = $user->getPhone();
        $code = $user->getConfirmationToken();
        /*$url = $this->router->generate('homepage');

        $event->setResponse(new RedirectResponse($url));*/
    }
}