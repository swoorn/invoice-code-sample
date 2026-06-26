<?php

declare(strict_types=1);

namespace App\Modules\Invoices\UseCases\ViewInvoice;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class HttpController
{
    use HandleTrait;

    public function __construct(
        MessageBusInterface $messageBus,
        private readonly ValidatorInterface $validator
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/invoices/{id}', name: 'invoices_view', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        $query = new ViewInvoiceQuery($id);

        $errors = $this->validator->validate($query);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var InvoiceViewDto|null $viewDto */
        $viewDto = $this->handle($query);

        return new JsonResponse($viewDto, JsonResponse::HTTP_OK);
    }
}
