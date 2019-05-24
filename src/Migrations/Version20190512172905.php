<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Username;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class Version20190512172905
 * @package DoctrineMigrations
 */
final class Version20190512172905 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function up(Schema $schema): void
    {
        $adminParam = $this->container->getParameter('app_admin_user');

        if (!$adminParam) {
            throw new \Exception('Admin username is not set!');
        }
        $adminUser = new Username();
        $adminUser->setUsername($adminParam);
        $this->container->get('doctrine.orm.entity_manager')->persist($adminUser);
        $this->container->get('doctrine.orm.entity_manager')->flush();
    }

    public function down(Schema $schema): void
    {
    }
}
