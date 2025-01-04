<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle;

use Stringkey\OptionMapperBundle\DependencyInjection\StringkeyOptionMapperExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class StringkeyOptionMapperBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new StringkeyOptionMapperExtension();
        }
        return $this->extension;
    }
}
