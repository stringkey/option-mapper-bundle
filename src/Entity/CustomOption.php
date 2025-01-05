<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringkey\EntityMapperBundle\Entity\Option\OptionGroup;
use Stringkey\EntityMapperBundle\Repository\CustomOptionRepository;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'custom_option')]
#[ORM\Index(columns: ['external_reference'])]
#[ORM\UniqueConstraint(fields: ["context", "optionGroup", "externalReference"])]
#[ORM\Entity(repositoryClass: CustomOptionRepository::class)]
class CustomOption
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
    #[ORM\ManyToOne(targetEntity: OptionGroup::class, inversedBy: 'customOptions')]
    protected OptionGroup $optionGroup;

    #[ORM\JoinColumn(name: 'context_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Context::class)]
    protected Context $context;

    #[ORM\Column(name: 'enabled', type: 'boolean', nullable: false)]
    private bool $enabled = false;

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

    use TimestampableEntity;

    // todo: Move to service when created
    public static function constructOption(
        OptionGroup $optionGroup,
        Context $context,
        string $name,
        string $externalReference,
        bool $enabled = true,
    ): self {
        $customOption = new self();

        $customOption->setOptionGroup($optionGroup);
        $customOption->setContext($context);
        $customOption->setName($name);
        $customOption->setExternalReference($externalReference);
        $customOption->setEnabled($enabled);

        return $customOption;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}