<?php

namespace App\Repository;

use App\Entity\Writer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Writer>
 *
 * @method Writer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Writer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Writer[]    findAll()
 * @method Writer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WriterRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Writer::class);
    }

    /**
     * 永続化
     * getEntityManegerの呼出を省略
     */
    public function save(Writer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * インスタンス削除
     * getEntityManegerの呼出を省略
     */
    public function remove(Writer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @see PasswordUpgraderInterface
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $writer, string $newHashedPassword): void
    {
        if (!$writer instanceof Writer) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($writer)));
        }

        $writer->setPassword($newHashedPassword);

        $this->save($writer, true);
    }
}
