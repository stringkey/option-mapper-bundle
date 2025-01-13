<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Form;

use Stringkey\OptionMapperBundle\Entity\ContextualOption;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionLinkType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'sourceOption',
            EntityType::class,
            [
                'class' => ContextualOption::class,
                'group_by' => function (ContextualOption $contextualOption) {
                    return $contextualOption->getContext();
                },
                'choice_label' => function (ContextualOption $contextualOption) {
                    return $contextualOption->getContext()->getName().'/'.$contextualOption->getName();
                },
            ]
        );
        $builder->add(
            'targetOption',
            EntityType::class,
            [
                'class' => ContextualOption::class,
                'group_by' => function (ContextualOption $contextualOption) {
                    return $contextualOption->getContext();
                },
                'choice_label' => function (ContextualOption $contextualOption) {
                    return $contextualOption->getContext()->getName().'/'.$contextualOption->getName();
                },
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
//        $resolver->setDefaults(['data_class' => OptionLink::class]);
    }
}
