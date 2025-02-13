<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Form;

use Stringkey\MapperBundle\Entity\MappableEntity;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Stringkey\OptionMapperBundle\Enum\GroupKind;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionGroupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class);
        $builder->add('groupKind', EnumType::class, ['class' => GroupKind::class]);
        $builder->add('mappableEntity', EntityType::class,
            [
                'class' => MappableEntity::class,
                'placeholder' => 'Optionally select a entity to map',
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
