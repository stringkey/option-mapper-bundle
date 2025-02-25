<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Form;

use Doctrine\ORM\Query\Expr\Join;
use Stringkey\MapperBundle\Entity\MappableEntity;
use Stringkey\MapperBundle\Repository\MappableEntityRepository;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Stringkey\OptionMapperBundle\Enum\GroupKind;
use Stringkey\OptionMapperBundle\Repository\OptionGroupRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Doctrine\ORM\QueryBuilder;

class OptionGroupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var OptionGroup $optionGroup */
        $optionGroup = $builder->getData();

        $builder->add('name', TextType::class);
        $builder->add('groupKind', EnumType::class, ['class' => GroupKind::class]);
        $builder->add('mappableEntity', EntityType::class,
            [
                'class' => MappableEntity::class,
                'placeholder' => 'Optionally select a entity to map',
                'query_builder' => function (MappableEntityRepository $mappableEntityRepository) use ($optionGroup) {
                    $queryBuilder = $mappableEntityRepository->getQueryBuilder();

                    $queryBuilder->leftJoin(
                        OptionGroup::class, OptionGroupRepository::ALIAS,
                        Join::WITH,
                        OptionGroupRepository::ALIAS . '.mappableEntity = '.MappableEntityRepository::ALIAS
                    );

                    $orExpression = $queryBuilder->expr()->orX();
                    // not linked
                    $orExpression->add($queryBuilder->expr()->isNull(OptionGroupRepository::ALIAS));

                    // Check if the OptionGroup is newly created
                    if ($optionGroup->getId() !== null) {
                        // or already linked to current
                        $orExpression->add($queryBuilder->expr()->eq(OptionGroupRepository::ALIAS, ':optionGroup'));

                        // current option group under edit
                        $queryBuilder->setParameter('optionGroup', $optionGroup->getId(), UuidType::NAME);
                    }

                    $queryBuilder->andWhere($orExpression);
                    return $queryBuilder;
                }
            ]
        );
        $builder->add(
            'masterContext',
            EntityType::class,
            [
                'class' => Context::class,
                'placeholder' => 'Optionally select a master context',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => OptionGroup::class]);
    }
}
