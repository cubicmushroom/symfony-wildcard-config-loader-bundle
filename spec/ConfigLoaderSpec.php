<?php

namespace spec\CubicMushroom\Symfony\WildcardConfigLoader;

use CubicMushroom\Symfony\WildcardConfigLoader\ConfigLoader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class ConfigLoaderSpec
 *
 * @package CubicMushroom\Symfony\WildcardConfigLoader
 *
 * @see \CubicMushroom\Symfony\WildcardConfigLoader\ConfigLoader
 */
class ConfigLoaderSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container, FileLocatorInterface $locator)
    {
        /** @var self|ConfigLoader $this */

        $this->beConstructedWith($container, $locator);
    }


    function it_is_initializable()
    {
        $this->shouldHaveType(ConfigLoader::class);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->shouldBeAnInstanceOf(FileLoader::class);
    }
    
    
    function it_should_support_paths_with_asterisks_in()
    {
        /** @var self|ConfigLoader $this */

        /** @noinspection PhpUndefinedMethodInspection */
        $this->supports('clients/*')->shouldReturn(true);
    }


    function it_should_not_support_paths_without_wildcards()
    {
        /** @var self|ConfigLoader $this */

        /** @noinspection PhpUndefinedMethodInspection */
        $this->supports('clients/123.yml')->shouldReturn(false);
    }


    function it_should_parse_a_yaml_file(ContainerBuilder $container, FileLocatorInterface $locator)
    {
        /** @var self|ConfigLoader $this */

        $filePath = 'clients/123.yml';
        $fullPath = __DIR__ . '/../test_files/clients/123.yml';

        /** @noinspection PhpUndefinedMethodInspection */
        $locator->locate($filePath)->willReturn($fullPath);

        $this->load($filePath, 'yml');

        $container->addResource(new FileResource($fullPath));
        /** @noinspection PhpVoidFunctionResultUsedInspection */ /** @noinspection PhpUndefinedMethodInspection */
        $container->setParameter('test.array.of', 'variables')->shouldHaveBeenCalled();
        /** @noinspection PhpVoidFunctionResultUsedInspection */ /** @noinspection PhpUndefinedMethodInspection */
        $container->setParameter('test.array.with', 'items')->shouldHaveBeenCalled();
    }
}
