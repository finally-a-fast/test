<?php

namespace fafcms\helpers;

use Yii;
use ReflectionClass;
use yii\log\Logger;

/**
 * Class ClassFinder
 * @package fafcms\helpers
 */
class ClassFinder
{
    /**
     * Find pathnames matching a pattern with ** wildcard support
     * Original by sebastian dot wasser at gmail dot com https://secure.php.net/manual/en/function.glob.php#119231
     * @param string $pattern
     * @param int $flags
     * @param bool $traversePostOrder
     * @return array
     */
    public static function rglob(string $pattern, int $flags = 0, $traversePostOrder = false): array
    {
        // Keep away the hassles of the rest if we don't use the wildcard anyway
        if (strpos($pattern, '/**/') === false) {
            return glob($pattern, $flags);
        }

        $patternParts = explode('/**/', $pattern);

        // Get sub dirs
        $dirs = glob(array_shift($patternParts) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);

        // Get files for current dir
        $files = glob($pattern, $flags);

        foreach ($dirs as $dir) {
            $subDirContent = self::rglob($dir . '/**/' . implode('/**/', $patternParts), $flags, $traversePostOrder);

            if (!$traversePostOrder) {
                $files = array_merge($files, $subDirContent);
            } else {
                $files = array_merge($subDirContent, $files);
            }
        }

        return $files;
    }

    /**
     * @param string $dir
     * @param array $filter
     * @param bool $getAdditionalClassData
     * @return array
     */
    public function findClasses(string $dir, array $filter = [], bool $getAdditionalClassData = false): array
    {
        $cacheName = self::class.hash('sha256', $dir.'_'.serialize($filter));
        $classes = Yii::$app->classFinderCache->get($cacheName);

        if ($classes === false) {
            $classes = [];
            $files = self::rglob(rtrim($dir, '/').'/*.php');

            foreach ($files as $file) {
                if (($class = $this->checkFile($file, $filter, $getAdditionalClassData)) !== null) {
                    $classes[] = $class;
                }
            }

            Yii::$app->classFinderCache->set($cacheName, $classes, null);
        }

        return $classes;
    }

    /**
     * @param string $file
     * @param array $filter
     * @param bool $getAdditionalClassData
     * @return array|null
     */
    private function checkFile(string $file, array $filter, bool $getAdditionalClassData): ?array
    {
        $fileContent = file_get_contents($file);
        $tokens = token_get_all($fileContent);

        $namespace = '';
        $class = '';

        for ($i = 0; $i < count($tokens); $i++) {
            if (is_array($tokens[$i])) {
                switch ($tokens[$i][0]) {
                    case T_NAMESPACE:
                        do {
                            $i++;
                        } while ($tokens[$i][0] !== T_STRING);

                        while (in_array($tokens[$i][0], [T_NS_SEPARATOR, T_STRING])) {
                            $namespace .= $tokens[$i][1];
                            $i++;
                        }

                        break;
                    case T_CLASS:
                        do {
                            $i++;
                        } while ($tokens[$i][0] !== T_STRING);

                        $class = $tokens[$i][1];
                        break 2;
                }
            }
        }

        if ($class === '' ||
            isset($filter['prefix']) && strpos($class, $filter['prefix']) !== 0 ||
            isset($filter['suffix']) && substr($class, -strlen($filter['suffix'])) !== $filter['suffix'] ||
            isset($filter['namespace']) && strpos($namespace, $filter['namespace']) !== 0
        ) {
            return null;
        }

        $fullyQualifiedClassName = $class;

        if ($namespace !== '') {
            $fullyQualifiedClassName = $namespace.'\\'.$fullyQualifiedClassName;
        }

        $additionalClassData = [];

        if ($getAdditionalClassData ||
            isset($filter['implements']) ||
            isset($filter['trait']) ||
            isset($filter['extends'])
        ) {
            if (!class_exists($fullyQualifiedClassName)) {
                return null;
            }

            try {
                $reflection = new ReflectionClass($fullyQualifiedClassName);
            } catch (\ReflectionException $e) {
                Yii::$app->getLog()->getLogger()->log($e->getMessage(), Logger::LEVEL_WARNING);
                return null;
            }

            $additionalClassData = [
                'interfaces' => $reflection->getInterfaceNames(),
                'traits' => $reflection->getTraitNames(),
                'extends' => $reflection->getParentClass()->name??null,
                'defaultProperties' => $reflection->getDefaultProperties()
            ];

            if (isset($filter['implements'])) {
                if (is_string($filter['implements'])) {
                    $filter['implements'] = [$filter['implements']];
                }

                foreach (($filter['implements']??[]) as $interface) {
                    if (!in_array($interface, $additionalClassData['interfaces'])) {
                        return null;
                    }
                }
            }

            if (isset($filter['traits'])) {
                if (is_string($filter['traits'])) {
                    $filter['traits'] = [$filter['traits']];
                }

                foreach (($filter['traits']??[]) as $trait) {
                    if (!in_array($trait, $additionalClassData['traits'])) {
                        return null;
                    }
                }
            }

            if (isset($filter['extends'])) {
                if ($filter['extends'] !== $additionalClassData['extends']) {
                    return null;
                }
            }
        }

        return array_merge([
            'fullyQualifiedClassName' => $fullyQualifiedClassName,
            'namespace' => $namespace,
            'class' => $class,
            'file' => $file
        ], $additionalClassData);
    }
}
