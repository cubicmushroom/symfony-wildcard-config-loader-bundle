<?php

namespace CubicMushroom\Symfony\WildcardConfigLoaderBundle;

use Symfony\Component\Config\FileLocator;

/**
 * Class GlobFileLocator
 *
 * @package CubicMushroom\Symfony\WildcardConfigLoaderBundle
 *
 * @see     \spec\CubicMushroom\Symfony\WildcardConfigLoaderBundle\GlobFileLocatorSpec for spec
 */
class GlobFileLocator extends FileLocator
{

    /**
     * {@inheritdoc}
     */
    public function locate($name, $currentPath = null, $first = true)
    {
        if ('' == $name) {
            throw new \InvalidArgumentException('An empty file name is not valid to be located.');
        }

        $paths = $this->paths;

        if ($this->isAbsolutePath($name)) {
            $paths[] = '';
        }

        if (null !== $currentPath) {
            array_unshift($paths, $currentPath);
        }

        $paths     = array_unique($paths);
        $filepaths = [];

        foreach ($paths as $path) {
            if (empty($path)) {
                $file = $name;
            } else {
                $file = $path . DIRECTORY_SEPARATOR . $name;
            }

            $globFiles = glob($file);

            foreach ($globFiles as $globFile) {
                if (file_exists($globFile)) {
                    if (true === $first) {
                        return $globFile;
                    }
                    $filepaths[] = realpath($globFile);
                }
            }
        }

        if (empty($filepaths)) {
            throw new \InvalidArgumentException(
                sprintf('The file "%s" does not exist (in: %s).', $name, implode(', ', $paths))
            );
        }

        return $filepaths;
    }


    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     *
     * @return bool
     */
    private function isAbsolutePath($file)
    {
        if ($file[0] === '/' || $file[0] === '\\'
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] === ':'
                && ($file[2] === '\\' || $file[2] === '/')
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
        ) {
            return true;
        }

        return false;
    }
}
