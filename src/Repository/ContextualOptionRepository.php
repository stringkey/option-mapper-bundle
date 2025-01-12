<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Entity\ContextualOption;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Symfony\Bridge\Doctrine\Types\UuidType;

class ContextualOptionRepository extends ServiceEntityRepository
{
    const ALIAS = 'contextualOption';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContextualOption::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder(self::ALIAS);

        $queryBuilder->orderBy(self::ALIAS . '.name', 'ASC');

        return $queryBuilder;
    }

    public function findOneByName(string $name, OptionGroup $optionGroup, Context $context): ?ContextualOption
    {
        $queryBuilder = $this->getQueryBuilder();

        self::addOptionGroupFilter($queryBuilder, $optionGroup);
        self::addContextFilter($queryBuilder, $context);
        self::addNameFilter($queryBuilder, $name);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
    
    /**
     * Find all target ContextualOption entities that are linked to the source Contextual options within a single context
     */
    public function findMappedOptions(OptionGroup $optionGroup, Context $sourceContext)
    {
        $queryBuilder = $this->getQueryBuilder();
        $sourceOptionAlias = 'sourceOption';
        $targetOptionAlias = 'targetOption';
        self::joinSourceToTarget($queryBuilder, $sourceOptionAlias, $targetOptionAlias);

        // You are selecting the target options, that are joined with a link to a source option
        // so we are working our way backwards from target to source
        self::addOptionGroupFilter($queryBuilder, $optionGroup, $sourceOptionAlias);
        self::addContextFilter($queryBuilder, $sourceContext, $sourceOptionAlias);

        ContextualOptionRepository::addContextFilter($queryBuilder, $sourceContext, $sourceOptionAlias);

        return $queryBuilder->getQuery()->getResult();
    }

    private static function joinSourceToTarget(QueryBuilder $queryBuilder, string $sourceAlias, string $targetAlias): void
    {
        // ContextualOption <source> -> Optionlink <OptionLink> <-- ContextualOption <target>
        OptionLinkRepository::joinSourceOption($queryBuilder, $sourceAlias);
        OptionLinkRepository::joinTargetOption($queryBuilder, $targetAlias);
    }

    /**
     * Function returns al options found based on the supplied names indexed by the externalReference.
     */
    public function findByNames(array $names, OptionGroup $optionGroup, Context $context): array
    {
        $queryBuilder = $this->createQueryBuilder(self::ALIAS, self::ALIAS . '.externalReference');

        self::addOptionGroupFilter($queryBuilder, $optionGroup);
        self::addContextFilter($queryBuilder, $context);
        self::addNamesFilter($queryBuilder, $names);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByExternalReference(
        string $externalReference,
        OptionGroup $optionGroup,
        Context $context,
    ): ?ContextualOption {
        $queryBuilder = $this->getQueryBuilder();

        self::addOptionGroupFilter($queryBuilder, $optionGroup);
        self::addContextFilter($queryBuilder, $context);
        self::addExternalReferenceFilter($queryBuilder, $externalReference);

        echo $queryBuilder->getQuery()->getSQL() . PHP_EOL;
        dump($queryBuilder->getParameters());
        
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
        $queryBuilder->andWhere($alias . '.name = :name');
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
    ): string
    {
        $parameterName = $alias . 'Context';

        $queryBuilder->andWhere($alias . '.context = :' . $parameterName);
        $queryBuilder->setParameter($parameterName, $context->getId(), UuidType::NAME);

        return $parameterName;
    }

    public static function addContextsFilter(
        QueryBuilder $queryBuilder,
        array $contexts,
        string $alias = self::ALIAS
    ): string {
        $parameterName = $alias . 'Contexts';
        // todo: Check if this works with a collection of objects that have Uuids as identifiers
        $queryBuilder->andWhere($alias . '.context IN (:' . $parameterName . ')');
        $queryBuilder->setParameter($parameterName, $contexts);

        return $parameterName;
    }

    public static function addOptionGroupFilter(
        QueryBuilder $queryBuilder,
        OptionGroup $optionGroup,
        $optionAlias = self::ALIAS
    ): void {
        $queryBuilder->andWhere($optionAlias . '.optionGroup = :optionGroup');
        $queryBuilder->setParameter('optionGroup', $optionGroup->getId(), UuidType::NAME);
    }

    public static function addExternalReferenceFilter(
        QueryBuilder $queryBuilder,
        string $externalReference,
        string $alias = self::ALIAS
    ): string {
        $parameterName = $alias . 'ExternalReference';

        $queryBuilder->andWhere($alias . '.externalReference = :' . $parameterName);
        $queryBuilder->setParameter($parameterName, $externalReference);

        return $parameterName;
    }

    public static function addExternalReferencesFilter(
        QueryBuilder $queryBuilder,
        array $externalReferences,
        string $alias = self::ALIAS
    ): string {
        $parameterName = $alias . 'ExternalReferences';

        $queryBuilder->andWhere($alias . '.externalReference IN (:'.$parameterName.')');
        $queryBuilder->setParameter($parameterName, $externalReferences);

        return $parameterName;
    }
}
