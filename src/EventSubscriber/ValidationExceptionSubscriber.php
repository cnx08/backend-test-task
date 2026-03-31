<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof HttpException) {
            return;
        }

        $previous = $exception->getPrevious();

        if (!$previous instanceof ValidationFailedException) {
            return;
        }

        $errors = [];
        foreach ($previous->getViolations() as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        $event->setResponse(new JsonResponse(['errors' => $errors], 422));
    }
}
