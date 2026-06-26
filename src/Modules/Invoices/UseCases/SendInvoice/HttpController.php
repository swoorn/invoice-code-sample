<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\SendInvoice;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class HttpController
{
    use HandleTrait {
        handle as handleCommand;
    }

    private MessageBusInterface $messageBus;

    public function __construct(
        MessageBusInterface $commandBus,
        private readonly ValidatorInterface $validator
    ) {
        $this->messageBus = $commandBus;
    }

    #[Route('/invoices/{id}/send', name: 'invoices_send', methods: ['POST'])]
    public function __invoke(string $id): JsonResponse
    {
        $command = new SendInvoiceCommand($id);

        $errors = $this->validator->validate($command);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->handleCommand($command);


        return new JsonResponse(['status' => 'Invoice processing successfully initiated.'], JsonResponse::HTTP_ACCEPTED);
    }
}
