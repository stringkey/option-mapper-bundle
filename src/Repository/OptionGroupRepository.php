<?php declare(strict_types = 1);

namespace Stringkey\OptionMapperBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Stringkey\OptionMapperBundle\Enum\GroupKind;

/**
 * @extends ServiceEntityRepository<OptionGroup>
 */
class OptionGroupRepository extends ServiceEntityRepository
{
    const ALIAS = 'optionGroup';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OptionGroup::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    /**
     * @param string $name
     * @return OptionGroup|null
     *
     * @throws NonUniqueResultException
     */
    public function findSystemGroupByName(string $name): ?OptionGroup
    {
        $queryBuilder = $this->getQueryBuilder();

        self::addNameFilter($queryBuilder, $name);
        self::addGroupKindFilter($queryBuilder, GroupKind::System);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findUserDefinedGroupByName(string $name): ?OptionGroup
    {
        $queryBuilder = $this->getQueryBuilder();

        self::addNameFilter($queryBuilder, $name);
        self::addGroupKindFilter($queryBuilder, GroupKind::UserDefined);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findOneByGroupKindAndName(string $name, GroupKind $groupKind): ?OptionGroup
    {
        $queryBuilder = $this->getQueryBuilder();

        self::addNameFilter($queryBuilder, $name);
        self::addGroupKindFilter($queryBuilder, $groupKind);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public static function addNameFilter(
        QueryBuilder $queryBuilder,
        string $name,
        string $alias = self::ALIAS
    ): void {
        $queryBuilder->andWhere($alias . '.name = :name');
        $queryBuilder->setParameter('name', $name);
    }

    public static function addGroupKindFilter(
        QueryBuilder $queryBuilder,
        GroupKind $groupKind,
        string $alias = self::ALIAS
    ): void {
        $queryBuilder->andWhere($alias . '.groupKind = :groupKind');
        $queryBuilder->setParameter('groupKind', $groupKind);
    }


}
