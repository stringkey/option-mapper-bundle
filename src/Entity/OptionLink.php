<?php

declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringkey\OptionMapperBundle\Repository\OptionLinkRepository;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'option_link')]
#[ORM\UniqueConstraint(fields: ['sourceOption', 'targetOption'])]
#[ORM\Entity(repositoryClass: OptionLinkRepository::class, readOnly: true)]
// todo: Check to configure this in an XML file to allow overrides by the end user?
#[UniqueEntity(
    fields: ['sourceOption', 'targetOption'],
    message: 'There is already a link between these options.'
)]
final class OptionLink
{
    use TimestampableEntity;
    public const SAME_CONTEXT_ERROR = '';
    public const DIFFERENT_GROUP_ERROR = '';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(name: 'ordinality', type: 'integer')]
    #[Gedmo\SortablePosition]
    protected int $ordinality = 0;

    #[ORM\Column(name: 'auto_resolve', type: 'boolean', nullable: false)]
    protected bool $autoResolve = false;

    #[ORM\JoinColumn(name: 'source_option_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: ContextualOption::class)]
    #[Gedmo\SortableGroup]
    protected ?ContextualOption $sourceOption;

    #[ORM\JoinColumn(name: 'target_option_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: ContextualOption::class)]
    protected ?ContextualOption $targetOption;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): void
    {
        $this->id = $id;
    }

    public function getOrdinality(): int
    {
        return $this->ordinality;
    }

    public function setOrdinality(int $ordinality): void
    {
        $this->ordinality = $ordinality;
    }

    public function isAutoResolve(): bool
    {
        return $this->autoResolve;
    }

    public function setAutoResolve(bool $autoResolve): void
    {
        $this->autoResolve = $autoResolve;
    }

    public function setSourceOption(?ContextualOption $contextualOption): static
    {
        $this->sourceOption = $contextualOption;

        return $this;
    }

    public function getSourceOption(): ContextualOption
    {
        return $this->sourceOption;
    }

    public function setTargetOption(ContextualOption $targetOption): static
    {
        $this->targetOption = $targetOption;

        return $this;
    }

    public function getTargetOption(): ContextualOption
    {
        return $this->targetOption;
    }

    public function removeTargetOption(): static
    {
        $this->targetOption = null;

        return $this;
    }

    public function removeSourceOption(): OptionLink
    {
        $this->sourceOption = null;

        return $this;
    }

    public function hasDifferentGroups(): bool
    {
        return $this->sourceOption->getOptionGroup() !== $this->targetOption->getOptionGroup();
    }

    public function hasSameContext(): bool
    {
        return $this->sourceOption->getContext() === $this->targetOption->getContext();
    }

    /**
     * @todo As per symfony bundle best practices the assertion should be configured as in XML format
     *       to allow the user to override the behavior
     *       see: https://symfony.com/doc/current/reference/constraints/Callback.html
     */
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->hasSameContext()) {
            $context->buildViolation('A link between two options must be between different contexts')
                ->atPath('targetOption')
                ->addViolation();
        }

        if ($this->hasDifferentGroups()) {
            $context->buildViolation('The linked options must be part of the same group')
                ->atPath('targetOption')
                ->addViolation();
        }
    }

    public function __toString(): string
    {
        return
            $this->sourceOption->getContext()->getName().'/'.$this->sourceOption->getName().
            ' -> '.
            $this->targetOption->getContext()->getName().'/'.$this->targetOption->getName();
    }
}
