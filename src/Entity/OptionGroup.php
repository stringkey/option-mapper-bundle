<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\OptionMapperBundle\Enum\GroupKind;
use Stringkey\OptionMapperBundle\Repository\OptionGroupRepository;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'option_group')]
#[ORM\UniqueConstraint(name: 'unique_name_idx', columns: ['name'])]
#[ORM\Entity(repositoryClass: OptionGroupRepository::class)]
class OptionGroup
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(name: 'name', type: 'string', unique: true)]
//  todo:  #[Assert\NotBlank(message: 'Please enter a name for the group.')]
    private string $name;

    #[ORM\Column(name: 'groupKind', type: 'string', nullable: false, enumType: GroupKind::class)]
    private GroupKind $groupKind = GroupKind::UserDefined;

    #[ORM\JoinColumn(name: 'master_context_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Context::class)]
    private ?Context $masterContext = null;

    #[ORM\OneToMany(targetEntity: ContextualOption::class, mappedBy: 'optionGroup', cascade: ['persist'])]
    private Collection $contextualOptions;

    public function __construct()
    {
        $this->contextualOptions = new ArrayCollection();
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

    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGroupKind(): GroupKind
    {
        return $this->groupKind;
    }

    public function setGroupKind(GroupKind $groupKind): static
    {
        $this->groupKind = $groupKind;

        return $this;
    }

    public function getContextualOptions(): array
    {
        return $this->contextualOptions->toArray();
    }

    public function addContextualOption(ContextualOption $contextualOption): static
    {
        if (!$this->contextualOptions->contains($contextualOption)) {
            $contextualOption->setOptionGroup($this);
            $this->contextualOptions->add($contextualOption);
        }

        return $this;
    }

    public function removeContextualOption(ContextualOption $contextualOption): static
    {
        $this->contextualOptions->removeElement($contextualOption);

        return $this;
    }

    public function setMasterContext(?Context $masterContext): OptionGroup
    {
        $this->masterContext = $masterContext;

        return $this;
    }

    public function getMasterContext(?Context $masterContext): ?Context
    {
        return $this->masterContext;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
