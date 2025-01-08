<?php declare(strict_types = 1);

namespace Stringkey\OptionMapperBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'option_link')]
#[ORM\UniqueConstraint(name: 'search_idx_option_link', fields: ['sourceOption', 'targetOption'])]
#[ORM\Entity]
class OptionLink
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(name: 'ordinality', type: 'integer')]
    #[Gedmo\SortablePosition]
    protected int $ordinality;

    #[ORM\Column(name: 'auto_resolve', type: 'boolean', nullable: false)]
    protected bool $autoResolve;

    #[ORM\JoinColumn(name: 'source_option_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: CustomOption::class, inversedBy: 'optionLinks')]
    #[Gedmo\SortableGroup]
    protected ?CustomOption $sourceOption;

    #[ORM\JoinColumn(name: 'target_option_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: CustomOption::class, inversedBy: 'optionLinks')]
    protected ?CustomOption $targetOption;

    use TimestampableEntity;

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

    public function setSourceOption(?CustomOption $customOption): static
    {
        $this->sourceOption = $customOption;
        $customOption->addOptionLink($this);

        return $this;
    }

    public function getSourceOption(): CustomOption
    {
        return $this->sourceOption;
    }

    public function setTargetOption(CustomOption $targetOption): static
    {
        $this->targetOption = $targetOption;
        $targetOption->addOptionLink($this);

        return $this;
    }

    public function getTargetOption(): CustomOption
    {
        return $this->targetOption;
    }

    public function removeTargetOption(): static
    {
        $this->targetOption = null;

        return $this;
    }

    public function removeSourceOption(): static
    {
        $this->sourceOption = null;

        return $this;
    }

    // todo: Move to service when created
    public static function construct(CustomOption $sourceOption, CustomOption $targetOption): static
    {
        $optionLink = new OptionLink();

        $optionLink->setSourceOption($sourceOption);
        $optionLink->setTargetOption($targetOption);

        return $optionLink;
    }
}
