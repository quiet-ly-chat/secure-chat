<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20190524120207
 * @package DoctrineMigrations
 */
final class Version20190524120207 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC6148D7472D');
        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC61F624B39D');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC6148D7472D FOREIGN KEY (accepter_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61F624B39D FOREIGN KEY (sender_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F2C24911F');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FBC1F5F3A');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2C24911F FOREIGN KEY (msg_to_id) REFERENCES fos_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FBC1F5F3A FOREIGN KEY (msg_from_id) REFERENCES fos_user (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC61F624B39D');
        $this->addSql('ALTER TABLE friend DROP FOREIGN KEY FK_55EEAC6148D7472D');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC61F624B39D FOREIGN KEY (sender_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE friend ADD CONSTRAINT FK_55EEAC6148D7472D FOREIGN KEY (accepter_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FBC1F5F3A');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F2C24911F');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FBC1F5F3A FOREIGN KEY (msg_from_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F2C24911F FOREIGN KEY (msg_to_id) REFERENCES fos_user (id)');
    }
}
