<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Entity\CustomOption;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;

class CustomOptionRepository extends ServiceEntityRepository
{
    const ALIAS = 'customOption';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomOption::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder(self::ALIAS);

        self::addEnabledFilter($queryBuilder, true);

        return $queryBuilder;
    }

    public function findOneByName(string $name, OptionGroup $optionGroup, Context $context): ?CustomOption
    {
        $queryBuilder = $this->getQueryBuilder();

        self::addOptionGroupFilter($queryBuilder, $optionGroup);
        self::addContextFilter($queryBuilder, $context);
        self::addNamesFilter($queryBuilder, [$name]);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Function returns al options found based on the supplied names indexed by the externalReference.
     */
    public function findByNames(array $names, OptionGroup $optionGroup, Context $context): array
    {
        $queryBuilder = $this->createQueryBuilder(self::ALIAS, self::ALIAS . '.externalReference');

        self::addEnabledFilter($queryBuilder, true);
        self::addOptionGroupFilter($queryBuilder, $optionGroup);
        self::addContextFilter($queryBuilder, $context);
        self::addNamesFilter($queryBuilder, $names);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByExternalReference(
        OptionGroup $optionGroup,
        Context $context,
        string $externalReference,
    ): ?CustomOption {
        $queryBuilder = $this->getQueryBuilder();

        self::addOptionGroupFilter($queryBuilder, $optionGroup);
        self::addContextFilter($queryBuilder, $context);
        self::addExternalReferenceFilter($queryBuilder, $externalReference);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public static function joinOptionGroup(QueryBuilder $queryBuilder, string $alias = self::ALIAS): void
    {
        $queryBuilder->join(
            OptionGroup::class,
            OptionGroupRepository::ALIAS,
            Join::WITH,
            $alias . '.optionGroup = ' . OptionGroupRepository::ALIAS
        );
    }

    public static function addNameFilter(QueryBuilder $queryBuilder, string $name, string $alias = self::ALIAS): void
    {
        $queryBuilder->andWhere($alias . '.name = :name)');
        $queryBuilder->setParameter('name', $name);
    }

    public static function addNamesFilter(QueryBuilder $queryBuilder, array $names, string $alias = self::ALIAS): void
    {
        $queryBuilder->andWhere($alias . '.name IN (:names)');
        $queryBuilder->setParameter('names', $names);
    }

    public static function addEnabledFilter(
        QueryBuilder $queryBuilder,
        bool $enabled = true,
        string $alias = self::ALIAS
    ): void {
        $queryBuilder->andWhere($queryBuilder->expr()->eq($alias . '.enabled', $enabled));
    }

    public static function addContextFilter(
        QueryBuilder $queryBuilder,
        Context $context,
        string $alias = self::ALIAS
    ): void
    {
        $queryBuilder->andWhere($alias . '.context = :context');
        $queryBuilder->setParameter('context', $context);
    }

    public static function addContextsFilter(
        QueryBuilder $queryBuilder,
        array $contexts,
        string $alias = self::ALIAS
    ): void {
        $queryBuilder->andWhere($alias . '.context IN (:contexts)');
        $queryBuilder->setParameter('contexts', $contexts);
    }

    public static function addOptionGroupFilter(QueryBuilder $queryBuilder, OptionGroup $optionGroup): void
    {
        $queryBuilder->andWhere(self::ALIAS . '.optionGroup = :optionGroup');
        $queryBuilder->setParameter('optionGroup', $optionGroup);
    }

    public static function addExternalReferenceFilter(
        QueryBuilder $queryBuilder,
        string $externalId,
        string $alias = self::ALIAS
    ): void {
        $queryBuilder->andWhere($alias . '.externalId = :externalId');
        $queryBuilder->setParameter('externalId', $externalId);
    }

    public static function addExternalReferencesFilter(
        QueryBuilder $queryBuilder,
        array $externalReferences,
        string $alias = self::ALIAS
    ): void {
        $queryBuilder->andWhere($alias . '.externalReference IN (:externalReferences)');
        $queryBuilder->setParameter('externalReferences', $externalReferences);
    }
}
