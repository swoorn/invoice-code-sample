<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Create invoice products table
 */
final class Version20260625154457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create invoice_products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice_products (
                id UUID NOT NULL,
                invoice_id UUID NOT NULL,
                name VARCHAR(255) NOT NULL,
                quantity INT NOT NULL,
                cents INT NOT NULL,
                PRIMARY KEY(id),
                CONSTRAINT FK_INVOICE_PRODUCTS_INVOICE FOREIGN KEY (invoice_id)
                    REFERENCES invoice (id) ON DELETE CASCADE
            )
            SQL);

        // Optional: Index on the foreign key for performance optimization
        $this->addSql('CREATE INDEX IDX_INVOICE_PRODUCTS_INVOICE_ID ON invoice_products (invoice_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE invoice_products');
    }
}
