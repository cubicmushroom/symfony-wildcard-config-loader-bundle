<?php

namespace CubicMushroom\Symfony\WildcardConfigLoader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
    const PARAMETER_TYPE_ARRAY = 'array';
    const PARAMETER_TYPE_FLAT  = 'flat';

    /**
     * How to add the parameters from the config file
     *
     * Should be one of self::PARAMETER_TYPE_*
     *
     * @var string
     */
    protected $parameterOutput;


    /**
     * ConfigLoader constructor.
     *
     * @param ContainerBuilder     $container
     * @param FileLocatorInterface $locator
     */
    public function __construct(ContainerBuilder $container, FileLocatorInterface $locator)
    {
        parent::__construct($container, $locator);

        $this->parameterOutput = self::PARAMETER_TYPE_ARRAY;
    }


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
        switch ($this->parameterOutput) {
            case self::PARAMETER_TYPE_ARRAY:
                foreach ($result as $key => $value) {
                    $this->container->setParameter($key, $value);
                }

                return;

            case self::PARAMETER_TYPE_FLAT;
                $parameters = $this->extractParameters((array)$result);

                foreach ($parameters as $key => $value) {
                    $this->container->setParameter($key, $value);
                }

                return;

            default:
                throw new \OutOfBoundsException("Unknown parameter type ({$this->parameterOutput}");
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


    /**
     * @param string $parameterOutput
     */
    public function setParameterOutput($parameterOutput)
    {
        if (!defined('self::PARAMETER_TYPE_'.strtoupper($parameterOutput))) {
            throw new \InvalidArgumentException("Unknown parameter type ({$parameterOutput})");
        }

        $this->parameterOutput = $parameterOutput;
    }
}
