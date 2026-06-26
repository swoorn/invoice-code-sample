<?php

declare(strict_types=1);

namespace App\Modules\Invoices\Infrastructure\Repositories;

use App\Modules\Invoices\Domain\Entities\Invoice;
use App\Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineInvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function save(Invoice $invoice): void
    {
        $this->entityManager->persist($invoice);
        // no flush here! command bus wraps execution in db transaction
    }

    public function findByIdWithItems(string $id): ?Invoice
    {
        return $this->entityManager->createQueryBuilder()
            ->select('i', 'items')
            ->from(Invoice::class, 'i')
            ->leftJoin('i.items', 'items')
            ->where('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findById(string $id): ?Invoice
    {
        return $this->entityManager->getRepository(Invoice::class)->findOneBy(['id' => $id]);
    }
}
