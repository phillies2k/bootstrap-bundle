<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\DependencyInjection;

use P2\Bundle\BootstrapBundle\Themeing\Theme;
use P2\Bundle\BootstrapBundle\Themeing\ThemeInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class P2BootstrapExtension
 * @package P2\Bundle\BootstrapBundle\DependencyInjection
 */
class P2BootstrapExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $this->generateThemes($container);

        if (isset($bundles['AsseticBundle'])) {
            $container->prependExtensionConfig(
                'assetic',
                array(
                    'filters' => array(
                        'less' => null
                    )
                )
            );
        }
    }

    protected function generateThemes(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('P2BootstrapBundle');
        $config = $this->processConfiguration(new Configuration(), $configs);

        foreach ($container->findTaggedServiceIds('bootstrap.theme') as $id => $attributes) {
            /** @var Theme $theme */
            $theme = $container->get($id);

            $themePath = $config['theme_path'] . '/' . $theme->getName();

            if (! is_dir($themePath)) {
                mkdir($themePath, 0777, true);
            }

            if (! file_exists($themePath . '/bootstrap.less')) {
                file_put_contents($themePath . '/bootstrap.less', $this->generateBootstrapLess($container));
            }

            if (! file_exists($themePath . '/layout.less')) {
                file_put_contents($themePath . '/layout.less', $this->generateLayoutLess($theme));
            }

            file_put_contents($themePath . '/variables.less', $this->generateVariablesLess($theme));

            $container->prependExtensionConfig(
                'assetic',
                array(
                    'assets' => $this->getAsseticThemeConfig($config, $theme)
                )
            );
        }
    }

    protected function getAsseticThemeConfig(array $config, ThemeInterface $theme)
    {
        $themeConfig = array();

        $themeConfig['theme_' . $theme->getName()] = array(
            'inputs' => $config['theme_path'] . '/layout.less',
            'output' => 'themes/' . $theme->getName() . '/style.css',
            'filter' => array('less')
        );

        return $themeConfig;
    }

    protected function getThemeVariables(ThemeInterface $theme)
    {
        $variables = array();

        if ($theme->getPrimaryColor() !== '') {
            $variables['brand-primary'] = $theme->getPrimaryColor();
        }

        if ($theme->getSuccessColor() !== '') {
            $variables['brand-success'] = $theme->getSuccessColor();
        }

        if ($theme->getWarningColor() !== '') {
            $variables['brand-warning'] = $theme->getWarningColor();
        }

        if ($theme->getDangerColor() !== '') {
            $variables['brand-danger'] = $theme->getDangerColor();
        }

        if ($theme->getInfoColor() !== '') {
            $variables['brand-info'] = $theme->getInfoColor();
        }

        if ($theme->getButtonDefaultColor() !== '') {
            $variables['btn-default-color'] = $theme->getButtonDefaultColor();
        }

        if ($theme->getButtonDefaultBackground() !== '') {
            $variables['btn-default-bg'] = $theme->getButtonDefaultBackground();
        }

        if ($theme->getButtonDefaultBorder() !== '') {
            $variables['btn-default-border'] = $theme->getButtonDefaultBorder();
        }

        if ($theme->getButtonPrimaryColor() !== '') {
            $variables['btn-primary-color'] = $theme->getButtonPrimaryColor();
        }

        if ($theme->getButtonSuccessColor() !== '') {
            $variables['btn-success-color'] = $theme->getButtonSuccessColor();
        }

        if ($theme->getButtonWarningColor() !== '') {
            $variables['btn-warning-color'] = $theme->getButtonWarningColor();
        }

        if ($theme->getButtonDangerColor() !== '') {
            $variables['btn-danger-color'] = $theme->getButtonDangerColor();
        }

        if ($theme->getButtonInfoColor() !== '') {
            $variables['btn-info-color'] = $theme->getButtonInfoColor();
        }

        return $variables;
    }

    protected function generateBootstrapLess(ContainerBuilder $container)
    {
        $lessPath = $container->getParameter('kernel.root_dir') . '/../vendor/twitter/bootstrap/less';
        $filePath = $lessPath . '/bootstrap.less';

        $pattern = '/@import\s"([^"]+)";/';
        $imports = array();

        if (false === $count = preg_match_all($pattern, file_get_contents($filePath), $matches)) {
            throw new \RuntimeException('preg_match_all encountered an error');
        }

        $template = "@import \"%s%s\";";
        for ($i = 0; $i < $count; $i++) {
            $imports[] = sprintf($template, $lessPath . '/', $matches[1][$i]);
        }

        $offset = array_search($lessPath . '/variables.less', $imports) + 1;
        array_splice($imports, $offset, 0, "@import \"variables.less\";");

        $contents = "// This file is auto generated.\n\n";
        $contents.= implode("\n", $imports);

        return $contents;
    }

    protected function generateLayoutLess(ThemeInterface $theme)
    {
        return <<<LESS_LAYOUT
// Theme: {$theme->getName()}
//
// Imports
// -------------------------------------------------
@import "bootstrap.less";

// Put your custom styles here

LESS_LAYOUT;

    }

    protected function generateVariablesLess(ThemeInterface $theme)
    {
        $contents = "";
        foreach ($this->getThemeVariables($theme) as $name => $value) {
            $contents .= "@" . $name . ": " . $value . ";\n";
        }

        return <<< VARIABLES_LESS
// Theme: {$theme->getName()}
//
// Variables
// -------------------------------------------------

{$contents}

VARIABLES_LESS;
    }
}
