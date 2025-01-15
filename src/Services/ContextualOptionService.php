<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Stringkey\MetadataCoreBundle\Entity\Context;
use Stringkey\MetadataCoreBundle\Repository\ContextRepository;
use Stringkey\OptionMapperBundle\Entity\ContextualOption;
use Stringkey\OptionMapperBundle\Entity\OptionGroup;
use Stringkey\OptionMapperBundle\Entity\OptionLink;
use Stringkey\OptionMapperBundle\Enum\GroupKind;
use Stringkey\OptionMapperBundle\Exception\IdenticalContextException;
use Stringkey\OptionMapperBundle\Exception\UnequalOptionGroupException;

class ContextualOptionService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public static function constructOption(
        OptionGroup $optionGroup,
        Context     $context,
        string      $name,
        string      $externalReference,
        bool        $enabled = true,
    ): ContextualOption {
        $contextualOption = new ContextualOption();

        $contextualOption->setOptionGroup($optionGroup);
        $contextualOption->setContext($context);
        $contextualOption->setName($name);
        $contextualOption->setExternalReference($externalReference);
        $contextualOption->setEnabled($enabled);

        return $contextualOption;
    }

    public function create(OptionLink $optionLink, bool $flushNow = true): void
    {
        $this->entityManager->persist($optionLink);
        if ($flushNow) {
            $this->entityManager->flush();
        }
    }

    /**
     * @throws IdenticalContextException
     * @throws UnequalOptionGroupException
     */
    public static function constructOptionLink(
        ContextualOption $sourceOption,
        ContextualOption $targetOption,
        int $ordinality = 0
    ): OptionLink {
        return self::construct($sourceOption, $targetOption, $ordinality);
    }
    public static function construct(
        ContextualOption $sourceOption,
        ContextualOption $targetOption,
        int $ordinality = 0
    ): OptionLink {
        $optionLink = new OptionLink();

        $optionLink->setSourceOption($sourceOption);
        $optionLink->setTargetOption($targetOption);
        $optionLink->setOrdinality($ordinality);

        // Check if the source and the target group are the same
        if ($optionLink->hasDifferentGroups()) {
            throw new UnequalOptionGroupException("Source and target options need to be part of the same option group");
        }

        // Check if the source and the target contexts are different
        if ($optionLink->hasSameContext()) {
            throw new IdenticalContextException("Source and target options must have different contexts");
        }

        return $optionLink;
    }

    public function findOrCreateOptionGroupByName(string $name, GroupKind $groupKind = GroupKind::UserDefined): OptionGroup
    {
        $optionGroupRepository  = $this->entityManager->getRepository(OptionGroup::class);
        $optionGroup = $optionGroupRepository->findOneBy(['name' => $name]);

        if (!$optionGroup) {
            $optionGroup = new OptionGroup();

            $optionGroup->setName($name);
            $optionGroup->setGroupKind($groupKind);

            $this->entityManager->persist($optionGroup);
            $this->entityManager->flush();
        }

        return $optionGroup;
    }

    public function findOrCreateContextByName(string $name): Context
    {
        /** @var ContextRepository $contextRepository */
        $contextRepository  = $this->entityManager->getRepository(Context::class);
        $context = $contextRepository->findByName($name);

        if (!$context) {
            $context = new Context();

            $context->setName($name);

            $this->entityManager->persist($context);
            $this->entityManager->flush();
        }

        return $context;
    }
}