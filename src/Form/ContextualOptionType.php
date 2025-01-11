<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Form;

use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Entity\ContextualOption;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContextualOptionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('optionGroup', EntityType::class, ['class' => OptionGroup::class]);
        $builder->add('name', TextType::class);
        $builder->add('externalReference', TextType::class);
        $builder->add('context', EntityType::class, ['class' => Context::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ContextualOption::class]);
    }
}
