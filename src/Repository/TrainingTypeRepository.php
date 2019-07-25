<?php

namespace App\Repository;

use App\Entity\TrainingType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TrainingTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TrainingType::class);
    }

    public function findAllForUser(User $user)
    {
        $qb = $this->createQueryBuilder('tt')
            ->leftJoin('tt.trainingTypePersons', 'ttp')
            ->leftJoin('ttp.person', 'ttpp')
            ->andWhere('ttpp.user = :user')
            ->setParameter('user', $user);

        return $qb->getQuery()->execute();
    }
}
