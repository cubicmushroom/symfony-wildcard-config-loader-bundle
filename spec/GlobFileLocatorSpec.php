<?php

namespace spec\CubicMushroom\Symfony\WildcardConfigLoaderBundle;

use CubicMushroom\Symfony\WildcardConfigLoaderBundle\GlobFileLocator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\FileLocator;

/**
 * Class GlobFileLocatorSpec
 *
 * @package CubicMushroom\Symfony\WildcardConfigLoaderBundle
 *
 * @see     \CubicMushroom\Symfony\WildcardConfigLoaderBundle\GlobFileLocator
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

        /** @noinspection PhpUndefinedMethodInspection */
        $this->locate('clients/*', __DIR__ . '/../test_files', false)->shouldReturn(
            [
                $testClientFilePath . '/123.yml',
                $testClientFilePath . '/456.yml',
            ]
        );
    }


    function it_should_handle_absolute_paths()
    {
        /** @var self|GlobFileLocator $this */

        $testClientFile = realpath(__DIR__ . '/../test_files/clients/123.yml');

        /** @noinspection PhpUndefinedMethodInspection */
        $this->locate($testClientFile)->shouldReturn($testClientFile);
    }
}
