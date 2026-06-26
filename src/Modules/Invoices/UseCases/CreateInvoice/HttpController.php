<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\CreateInvoice;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        private readonly MessageBusInterface $commandBus,
        private readonly ValidatorInterface $validator
    ) {
        $this->messageBus = $this->commandBus;
    }

    #[Route('/invoices', name: 'invoices_create', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Malformed JSON request.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $command = CreateInvoiceCommand::fromArray($data);

        $errors = $this->validator->validate($command);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var CreateInvoiceResult $result */
        $result = $this->handleCommand($command);

        return new JsonResponse([
            'id' => $result->id
        ], JsonResponse::HTTP_CREATED);
    }
}
