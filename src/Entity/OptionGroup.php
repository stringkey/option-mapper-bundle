<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
    protected string $name;

    #[ORM\Column(name: 'groupKind', type: 'string', nullable: false, enumType: GroupKind::class)]
    protected GroupKind $groupKind = GroupKind::UserDefined;

    #[ORM\OneToMany(targetEntity: CustomOption::class, mappedBy: 'optionGroup', cascade: ['persist'])]
    private Collection $customOptions;

    public function __construct()
    {
        $this->customOptions = new ArrayCollection();
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

    public function getCustomOptions(): array
    {
        return $this->customOptions->toArray();
    }

    public function addCustomOption(CustomOption $customOption): static
    {
        if (!$this->customOptions->contains($customOption)) {
            $customOption->setOptionGroup($this);
            $this->customOptions->add($customOption);
        }

        return $this;
    }

    public function removeCustomOption(CustomOption $customOption): static
    {
        $this->customOptions->removeElement($customOption);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
