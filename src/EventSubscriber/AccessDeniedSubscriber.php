<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessDeniedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RouterInterface $router,
        private RequestStack $requestStack
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onAccessDenied',
        ];
    }

    public function onAccessDenied(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedException && !$exception instanceof AccessDeniedHttpException) {
            return;
        }

        $request = $event->getRequest();

        if ($request->isXmlHttpRequest()) {
            return;
        }

        // Ajouter un message flash via la session
        $session = $this->requestStack->getSession();
        $session->set('_security.access_denied_error', 'Vous n\'avez pas accès à cette ressource.');

        $url = $this->router->generate('project_index');
        $event->setResponse(new RedirectResponse($url));
    }
}
