<?php

declare(strict_types=1);

namespace App\Modules\Shared\Infrastructure;

use App\Modules\Shared\Exceptions\DomainLogicException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[AsEventListener(event: 'kernel.exception')]
final class DomainExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious() ?? $exception;
        }

        if ($exception instanceof DomainLogicException) {
            $response = new JsonResponse([
                'error' => $exception->getMessage()
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

            $event->setResponse($response);
        }
    }
}
