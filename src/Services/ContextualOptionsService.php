<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Stringkey\OptionMapperBundle\Entity\CustomOption;
use Stringkey\OptionMapperBundle\Entity\OptionLink;
use Stringkey\OptionMapperBundle\Exception\UnequalOptionGroupException;

class ContextualOptionsService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public function create(OptionLink $optionLink, bool $flushNow = true): void
    {
        $this->entityManager->persist($optionLink);
        if ($flushNow) {
            $this->entityManager->flush();
        }
    }

    public static function constructOptionLink(
        CustomOption $sourceOption,
        CustomOption $targetOption,
        int $ordinality = 0
    ): OptionLink {

        // Check if the source and the target group are the same
        if ($sourceOption->getOptionGroup() !== $targetOption->getOptionGroup()) {
            throw new UnequalOptionGroupException();
        }

        $optionLink = new OptionLink();

        $optionLink->setSourceOption($sourceOption);
        $optionLink->setTargetOption($targetOption);
        $optionLink->setOrdinality($ordinality);

        return $optionLink;
    }
}