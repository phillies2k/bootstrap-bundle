<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\DependencyInjection\Compiler;

use P2\Bundle\BootstrapBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ThemePass
 * @package P2\Bundle\BootstrapBundle\DependencyInjection\Compiler
 */
class ThemePass implements CompilerPassInterface
{
    const LESS_BOOTSTRAP = <<<LESS_BOOTSTRAP
// This file is auto generated. Do not edit.

// imports
%imports%

// bootstrap extensions

.form-control-inline {
    display: inline-block;
    width: auto;
    + .form-control-inline {
        margin-left: @base-padding-horizontal;
    }
}

LESS_BOOTSTRAP;


    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('p2_bootstrap.theme_builder')) {
            throw new \RuntimeException('Missing theme builder service "p2_bootstrap.theme_builder".');
        }

        $processor = new Processor();
        $configs = $container->getExtensionConfig('p2_bootstrap');
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $this->symlinkFonts($config, $container);

        if ($config['use_themes'] === true) {
            $this->buildBootstrapLess($config, $container);

            $themeBuilder = $container->getDefinition('p2_bootstrap.theme_builder');

            foreach ($container->findTaggedServiceIds('bootstrap.theme') as $id => $attributes) {
                $themeBuilder->addMethodCall('addTheme', array(new Reference($id)));
            }
        }
    }

    /**
     * Creates the bootstrap.less file for your themes.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function buildBootstrapLess(array $config, ContainerBuilder $container)
    {
        $filepath = $container->getParameterBag()->resolveValue($config['themes_path']);

        if (! is_dir($filepath)) {
            mkdir($filepath, 0777, true);
        }

        file_put_contents($filepath . '/bootstrap.less', $this->generateBootstrapLess($config, $container));
    }

    /**
     * Generates the bootstrap.less file for the given theme.
     *
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function generateBootstrapLess(array $config, ContainerBuilder $container)
    {
        $relativePath = $this->getRelativeBootstrapPath($config, $container);
        $template = "@import \"%s\";\n";
        $contents = "";

        foreach ($this->parseImports($config, $container) as $filepath) {
            if ($filepath !== 'variables.less') {
                $contents .= sprintf($template, $relativePath . '/less/' . $filepath);
            }
        }

        return strtr(static::LESS_BOOTSTRAP, array('%imports%' => $contents));
    }

    /**
     * Parses import statements from bootstrap.less and returns an array of its values.
     *
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return array
     * @throws \RuntimeException
     */
    protected function parseImports(array $config, ContainerBuilder $container)
    {
        $bootstrapDirectory = $container->getParameterBag()->resolveValue($config['source_path']);
        $bootstrapFilepath = $bootstrapDirectory . '/less/bootstrap.less';
        $bootstrapContents = file_get_contents($bootstrapFilepath);

        if (false === $count = preg_match_all('/@import\s"([^"]+)";/', $bootstrapContents, $matches)) {
            throw new \RuntimeException('preg_match_all encountered an error');
        }

        if ($count === 0 || ! isset($matches[1]) || ! is_array($matches[1])) {

            return array();
        }

        return $matches[1];
    }

    /**
     * Returns the relative path to the twitter bootstrap directory.
     *
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return string
     */
    protected function getRelativeBootstrapPath(array $config, ContainerBuilder $container)
    {
        $bootstrapPath = realpath($container->getParameterBag()->resolveValue($config['source_path']));
        $rootPath = realpath($container->getParameter('kernel.root_dir') . '/../');

        return $this->getRelativeRootPath($config, $container) . ltrim(substr($bootstrapPath, strlen($rootPath)), '/');
    }

    /**
     * Returns the relative path to the project root.
     *
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return string
     */
    protected function getRelativeRootPath(array $config, ContainerBuilder $container)
    {
        $themeDirectory = realpath($container->getParameterBag()->resolveValue($config['themes_path']));
        $rootDirectory = realpath($container->getParameter('kernel.root_dir') . '/..');
        $path = substr($themeDirectory, strlen($rootDirectory) + 1);
        $step = count(explode('/', $path));

        return str_repeat('../', $step);
    }

    /**
     * Adds theme symlinks for bootstraps glyphicon font.
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function symlinkFonts(array $config, ContainerBuilder $container)
    {
        $origin = $container->getParameterBag()->resolveValue($config['source_path']) . '/fonts';
        $target = $container->getParameter('kernel.root_dir') . '/../web/fonts';

        $filesystem = new Filesystem();
        if (! $filesystem->exists($target)) {
            $filesystem->symlink($origin, $target);
        }
    }
}
