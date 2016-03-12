Symfony Wildcard Config Loader Bundle
=====================================

This bundle provides an easy way to load in multiple config files using a glob pattern to match files.
 
 
Usage
-----

To use the config loader, you need to update your AppKernel to add a new instance of 
\CubicMushroom\Symfony\WildcardConfigLoaderBundle\ConfigLoader as a config loader by overriding the 
AppKernel::getContainerLoader() method as follows...


````
use CubicMushroom\Symfony\WildcardConfigLoaderBundle\ConfigLoader;
use CubicMushroom\Symfony\WildcardConfigLoaderBundle\GlobFileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppKernel
{
    // ...

    /**
     * Add loader to load client config files (app/config/clients/*)
     *
     * @param ContainerInterface $container
     *
     * @return DelegatingLoader
     *
     * @throws RuntimeException
     */
    protected function getContainerLoader(ContainerInterface $container)
    {
        // Container must be an instance of ContainerBuilder
        if (!$container instanceof ContainerBuilder) {
            throw new \RuntimeException(
                sprintf(
                    'Expected loader to be instance of %s, but got %s',
                    ContainerBuilder::class,
                    get_class($container)
                )
            );
        }

        $cl = parent::getContainerLoader($container);

        // Add additional loader to the resolver
        $resolver = $cl->getResolver();

        if (!$resolver instanceof LoaderResolver) {
            throw new \RuntimeException(
                sprintf(
                    'Expected container to be instance of %s, but got %s',
                    LoaderResolver::class,
                    get_class($resolver)
                )
            );
        }

        $resolver->addLoader(new ConfigLoader($container, new GlobFileLocator([])));

        return $cl;
    }
}
````


Which files to handle
---------------------

By default this config loader will handle any file strings containing an asterisk (\*).  If you'd like to change which 
files the class handles, simply extends the class and override the supports() method.


Config values
-------------

Config values found in the files will be stored in array parameters.  For example, yml config file content like this...

````
client:
    here:
        this: 123
        that: 456
    now:
        something: else
````

... would translate to the following parameters...

````
client = [
    'here' => [
        'this' => 123,
        'that' => 456,
    ],
    'something' => 'else',
]
````


This is using the details ConfigLoader::PARAMETER_TYPE_ARRAY option.  You can change this if you prefer flat parameter, 
by calling the `setParameterOutput()` method with `ConfigLoader::FLAT` as the only argument. 

````
client.here.this = 123
client.here.that = 456
client.now.something = else
````


### Overriding the default parameter storage

If you'd like to override the default storing of the config variables you can extend the `ConfigLoader` class and 
override the setParameters() method.