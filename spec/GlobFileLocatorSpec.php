<?php

namespace spec\CubicMushroom\Symfony\WildcardConfigLoader;

use CubicMushroom\Symfony\WildcardConfigLoader\GlobFileLocator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\FileLocator;

/**
 * Class GlobFileLocatorSpec
 *
 * @package CubicMushroom\Symfony\WildcardConfigLoader
 *
 * @see     \CubicMushroom\Symfony\WildcardConfigLoader\GlobFileLocator
 */
class GlobFileLocatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GlobFileLocator::class);
        $this->shouldBeAnInstanceOf(FileLocator::class);
    }
}
