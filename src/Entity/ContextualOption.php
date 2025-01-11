<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Repository\ContextualOptionRepository;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'custom_option')]
#[ORM\Index(columns: ['external_reference'])]
#[ORM\UniqueConstraint(fields: ["context", "optionGroup", "externalReference"])]
#[ORM\Entity(repositoryClass: ContextualOptionRepository::class)]
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

    #[ORM\OneToMany(targetEntity: OptionLink::class, mappedBy: 'contextualOption', cascade: ['persist'])]
    protected Collection $optionLinks;

    #[ORM\Column(name: 'enabled', type: 'boolean', nullable: false)]
    private bool $enabled = false;

    use TimestampableEntity;

    public function __construct() {
        $this->optionLinks = new ArrayCollection();
    }

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

    public function getExternalReference(): string
    {
        return $this->externalReference;
    }

    public function setExternalReference(string $externalReference): static
    {
        $this->externalReference = $externalReference;

        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    public function getOptionGroup(): OptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(OptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
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

    public function getOptionLinks(): array
    {
        return $this->optionLinks->toArray();
    }

    public function addOptionLink(OptionLink $optionLink): static
    {
        $this->optionLinks->add($optionLink);

        return $this;
    }

    public function removeOptionLink(OptionLink $optionLink): static
    {
        $this->optionLinks->removeElement($optionLink);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}