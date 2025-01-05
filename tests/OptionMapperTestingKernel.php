<?php declare(strict_types=1);

namespace Stringkey\OptionMapperBundle\Tests;

use Stringkey\OptionMapperBundle\StringkeyOptionMapperBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class OptionMapperTestingKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new StringkeyOptionMapperBundle()
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {

    }
}