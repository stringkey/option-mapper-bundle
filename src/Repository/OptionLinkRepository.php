<?php

declare(strict_types = 1);

namespace Stringkey\OptionMapperBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Stringkey\OptionMapperBundle\Entity\ContextualOption;
use Stringkey\OptionMapperBundle\Entity\OptionLink;
use Symfony\Bridge\Doctrine\Types\UuidType;

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

    /**
     * @return string The alias for the source
     */
    public static function joinSourceOption(
        QueryBuilder $queryBuilder,
        string $sourceAlias = 'sourceOption',
        string $linkAlias = self::ALIAS
    ): string {
        $queryBuilder->join(
            ContextualOption::class,
            $sourceAlias,
            Join::WITH,
            $linkAlias . '.sourceOption = ' . $sourceAlias
        );

        return $sourceAlias;
    }

    /**
     * @return string The alias for the target
     */
    public static function joinTargetOption(
        QueryBuilder $queryBuilder,
        string $targetAlias = 'targetOption',
        string $linkAlias = self::ALIAS
    ): string {
        $queryBuilder->join(
            ContextualOption::class,
            $targetAlias,
            Join::WITH,
            $linkAlias . '.targetOption = ' . $targetAlias
        );

        return $targetAlias;
    }

    public static function leftJoinTargetOption(
        QueryBuilder $queryBuilder,
        string $targetOptionAlias = 'targetOption',
        string $linkAlias = OptionLinkRepository::ALIAS,
    ): void {
        if (!in_array($targetOptionAlias, $queryBuilder->getAllAliases())) {
            $queryBuilder->leftJoin(
                ContextualOption::class,
                $targetOptionAlias,
                Join::WITH,
                $linkAlias.'.targetOption = '.$targetOptionAlias
            );
        }
    }

    public static function addSourceOptionFilter(QueryBuilder $queryBuilder, ContextualOption $contextualOption, string $alias = self::ALIAS): void
    {
        $queryBuilder->andWhere($alias . '.sourceOption = :sourceOption');
        $queryBuilder->setParameter('sourceOption', $contextualOption->getId(), UuidType::NAME);
    }

    public static function addTargetOptionFilter(QueryBuilder $queryBuilder, ContextualOption $contextualOption, string $alias = self::ALIAS): void
    {
        $queryBuilder->andWhere($alias . '.targetOption = :targetOption');
        $queryBuilder->setParameter('targetOption', $contextualOption->getId(), UuidType::NAME);
    }

    public static function addHasAutoResolveFilter(QueryBuilder $queryBuilder, string $alias = self::ALIAS): void
    {
        self::addAutoResolveFilter($queryBuilder, true, $alias);
    }

    public static function addAutoResolveFilter(QueryBuilder $queryBuilder, bool $autoResolve, string $alias = self::ALIAS): void
    {
        $queryBuilder->andWhere($alias . '.autoResolve = :autoResolve');
        $queryBuilder->setParameter('autoResolve', $autoResolve);
    }
}
