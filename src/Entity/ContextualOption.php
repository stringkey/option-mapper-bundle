<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Repository\ContextualOptionRepository;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'custom_option')]
#[ORM\Index(columns: ['external_reference'])]
#[ORM\UniqueConstraint(fields: ["context", "optionGroup", "externalReference"])]
#[ORM\UniqueConstraint(fields: ["context", "optionGroup", "name"])]
#[ORM\Entity(repositoryClass: ContextualOptionRepository::class)]
#[UniqueEntity(
    fields: ['name', 'optionGroup', 'context'],
    message: 'The option "{{ name }}" already exists for this context in this group.',
    errorPath: 'name',
)]
#[UniqueEntity(
    fields: ['externalReference', 'optionGroup', 'context'],
    message: 'The option "{{ name }}" already exists with this reference within this context and group.',
    errorPath: 'externalReference',
)]
class ContextualOption
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(name: 'name', type: 'string')]
    protected string $name;

    #[ORM\Column(name: 'external_reference', type: 'string')]
    protected string $externalReference;

    #[ORM\JoinColumn(name: 'option_group_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: OptionGroup::class, inversedBy: 'contextualOptions')]
    protected OptionGroup $optionGroup;

    #[ORM\JoinColumn(name: 'context_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Context::class)]
    protected Context $context;

    #[ORM\Column(name: 'enabled', type: 'boolean', nullable: false)]
    private bool $enabled = false;

    use TimestampableEntity;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getExternalReference(): string
    {
        return $this->externalReference;
    }

    public function setExternalReference(string $externalReference): static
    {
        $this->externalReference = $externalReference;

        return $this;
    }

    public function getOptionGroup(): OptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(OptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }

    public function setContext(Context $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
