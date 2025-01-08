<?php declare(strict_types = 1);

namespace Stringkey\OptionMapperBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Stringkey\OptionMapperBundle\Entity\OptionLink;

/**
 * @extends ServiceEntityRepository<OptionLink>
 */
class OptionLinkRepository extends ServiceEntityRepository
{
    const ALIAS = 'optionLink';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OptionLink::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
