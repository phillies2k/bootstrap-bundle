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
use P2\Bundle\BootstrapBundle\Themeing\ThemeInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ThemePass
 * @package P2\Bundle\BootstrapBundle\DependencyInjection\Compiler
 */
class ThemePass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('assetic.config_resource')) {
            throw new \RuntimeException('Missing assetic bundle.');
        }

        if (! $container->hasDefinition('p2_bootstrap.theme_builder')) {
            throw new \RuntimeException('Missing theme builder service "p2_bootstrap.theme_builder".');
        }

        $resourcesConfig = $container->getDefinition('assetic.config_resource')->getArgument(0);
        $extensionConfig = $this->getExtensionConfiguration($container);

        $this->buildBootstrapLess($extensionConfig, $container);
        $this->symlinkFonts($extensionConfig, $container);

        $themeBuilder = $container->getDefinition('p2_bootstrap.theme_builder');

        foreach ($container->findTaggedServiceIds('bootstrap.theme') as $id => $attributes) {
            $themeBuilder->addMethodCall('buildTheme', array(new Reference($id)));
            $theme = $container->get($id);
            $themeConfig = $this->buildAsseticThemeConfig($extensionConfig, $container, $theme);
            $resourcesConfig = array_merge($resourcesConfig, $themeConfig);
        }

        $container->getDefinition('assetic.config_resource')->replaceArgument(0, $resourcesConfig);
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
        $contents = "// This file is auto generated.\n\n";

        foreach ($this->parseImports($config, $container) as $filepath) {
            if ($filepath !== 'variables.less') {
                $contents .= sprintf($template, $relativePath . '/' . $filepath);
            }
        }

        return $contents;
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
     * Returns the relative path to the twitter bootstrap directory for the given theme.
     *
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return string
     */
    protected function getRelativeBootstrapPath(array $config, ContainerBuilder $container)
    {
        $bootstrapPath = $container->getParameterBag()->resolveValue($config['source_path']) . '/less';
        $rootPath = $container->getParameter('kernel.root_dir') . '/../';

        return $this->getRelativeRootPath($config, $container) . substr($bootstrapPath, strlen($rootPath));
    }

    /**
     * Returns the relative path to the project root for the given theme.
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
        $pattern = $container->getParameterBag()->resolveValue($config['source_path']) . '/fonts/*';
        $rootPath = $container->getParameter('kernel.root_dir') . '/../web';
        $fontPath = $rootPath . '/' . $container->getParameterBag()->resolveValue($config['public_path']) . '/fonts';

        if (! is_dir($fontPath)) {
            mkdir($fontPath, 0777, true);
        }

        foreach (glob($pattern) as $filepath) {
            $distPath = $fontPath . '/' . basename($filepath);
            if (! file_exists($distPath)) {
                symlink($filepath, $distPath);
            }
        }
    }

    /**
     * Returns the assetic configuration entry for the given theme.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     *
     * @return array
     */
    protected function buildAsseticThemeConfig(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $themeConfig = array();

        $themeConfig['theme_' . $theme->getName()] = array(
            array($this->resolveThemePath($config['themes_path'], $container, $theme) . '/less/layout.less'),
            array('less'),
            array('output' => $this->resolveThemePath($config['public_path'], $container, $theme) . '/css/style.css'),
        );

        return $themeConfig;
    }

    /**
     * Returns the resolved path for the given theme.
     *
     * @param string $path
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     *
     * @return string
     */
    protected function resolveThemePath($path, ContainerBuilder $container, ThemeInterface $theme)
    {
        return $container->getParameterBag()->resolveValue($path) . '/' . $theme->getName();
    }

    /**
     * Returns the bundles processed extension configuration.
     *
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function getExtensionConfiguration(ContainerBuilder $container)
    {
        $config = $container->getExtensionConfig('p2_bootstrap');
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $config);
    }
}
