<?php

namespace App\Repository;

use App\Entity\Username;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Username|null find($id, $lockMode = null, $lockVersion = null)
 * @method Username|null findOneBy(array $criteria, array $orderBy = null)
 * @method Username[]    findAll()
 * @method Username[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsernameRepository extends ServiceEntityRepository
{
    /**
     * UsernameRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Username::class);
    }
}
