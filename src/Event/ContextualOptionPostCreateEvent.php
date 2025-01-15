<?php

declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Event;

use Stringkey\OptionMapperBundle\Entity\ContextualOption;
use Symfony\Contracts\EventDispatcher\Event;

final class ContextualOptionPostCreateEvent extends Event
{
    public function __construct(private readonly ContextualOption $contextualOption) {}

    public function getContextualOption(): ContextualOption
    {
        return $this->contextualOption;
    }
}