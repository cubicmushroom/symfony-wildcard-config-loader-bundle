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
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(FileLocator::class);
    }


    function it_should_locate_file_with_a_glob_pattern()
    {
        /** @var self|GlobFileLocator $this */

        $testClientFilePath = realpath(__DIR__ . '/../test_files/clients');

        $this->locate('clients/*', __DIR__ . '/../test_files', false)->shouldReturn(
            [
                $testClientFilePath . '/123.yml',
                $testClientFilePath . '/456.yml',
            ]
        );
    }
}
