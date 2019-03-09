<?php

namespace App\Repository;

use App\Entity\Board;
use App\Entity\Stage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Stage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stage[]    findAll()
 * @method Stage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Stage::class);
    }

    /**
     * @param string $title
     * @param Board $board
     *
     * @return Stage
     */
    public function create(
        string $title,
        Board $board
    ) {
        $stage = new Stage($title, $board);

        return $this->save($stage);
    }

    /**
     * @param \App\Entity\Stage $stage
     *
     * @return \App\Entity\Stage
     */
    public function save(Stage $stage): Stage
    {
        $this->_em->persist($stage);
        $this->_em->flush();

        return $stage;
    }
}
