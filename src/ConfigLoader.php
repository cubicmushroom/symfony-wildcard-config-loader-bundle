<?php

namespace CubicMushroom\Symfony\WildcardConfigLoader;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;


/**
 * Class ConfigLoader
 *
 * @package CubicMushroom\Symfony\WildcardConfigLoader
 *
 * @see     \spec\CubicMushroom\Symfony\WildcardConfigLoader\ConfigLoaderSpec for spec
 */
class ConfigLoader extends FileLoader
{
    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource
     * @param string|null $type     The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return (false !== strpos($resource, '*'));
    }


    /**
     * Loads a resource.
     *
     * @param mixed       $file The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @throws ParseException If the YAML is not valid
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);

        $this->container->addResource(new FileResource($path));

        $yamlParser = new Parser();

        $result = $yamlParser->parse(file_get_contents($path));
        $this->setParameters($result);
    }


    /**
     * Takes an array, read from config file and processes it
     *
     * This method can be overridden in child classes in order to store the config differently
     *
     * @param array $result
     */
    protected function setParameters(array $result)
    {
        $parameters = $this->extractParameters((array)$result);

        foreach ($parameters as $key => $value) {
            $this->container->setParameter($key, $value);
        }
    }


    /**
     * @param array  $data       Data to be converted
     * @param string $prefix     Prefix used for parameters during recursion
     * @param array  $parameters Parameters array build up & returned
     *
     * @return array
     */
    protected function extractParameters(array $data, $prefix = '', $parameters = [])
    {
        foreach ($data as $key => $value) {
            $currentPrefix = ltrim($prefix . '.' . $key, '.');

            if (is_array($value)) {
                $parameters = $this->extractParameters($value, $currentPrefix, $parameters);
            } else {
                $parameters[$currentPrefix] = $value;
            }
        }

        return $parameters;
    }
}
