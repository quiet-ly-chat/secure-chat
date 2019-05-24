<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190503131551
 * @package DoctrineMigrations
 */
final class Version20190503131551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, msg_from_id INT NOT NULL, msg_to_id INT NOT NULL, send_date DATETIME NOT NULL, from_msg LONGTEXT NOT NULL, to_msg LONGTEXT NOT NULL, INDEX IDX_B6BD307FBC1F5F3A (msg_from_id), INDEX IDX_B6BD307F2C24911F (msg_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FBC1F5F3A FOREIGN KEY (msg_from_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2C24911F FOREIGN KEY (msg_to_id) REFERENCES fos_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE message');
    }
}
