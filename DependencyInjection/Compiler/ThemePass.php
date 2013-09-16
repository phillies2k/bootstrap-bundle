<?php
/**
 * This file is part of the MTN project.
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

/**
 * Class ThemePass
 * @package P2\Bundle\BootstrapBundle\DependencyInjection\Compiler
 */
class ThemePass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('p2_bootstrap');
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);
        $path = $container->getParameterBag()->resolveValue($config['theme_path']);
        $asseticConfig = $container->getDefinition('assetic.config_resource')->getArgument(0);

        foreach ($container->findTaggedServiceIds('bootstrap.theme') as $id => $attributes) {
            /** @var ThemeInterface $theme */
            $theme = $container->get($id);

            $themePath = $path . '/' . $theme->getName();
            $lessPath = $themePath . '/less';

            if (! is_dir($lessPath)) {
                mkdir($lessPath, 0777, true);
            }

            if (! file_exists($lessPath . '/bootstrap.less')) {
                file_put_contents($lessPath . '/bootstrap.less', $this->generateBootstrapLess($container));
            }

            if (! file_exists($lessPath . '/layout.less')) {
                file_put_contents($lessPath . '/layout.less', $this->generateLayoutLess($theme));
            }

            file_put_contents($lessPath . '/variables.less', $this->generateVariablesLess($theme));

            $themeConfig = $this->getAsseticThemeConfig($lessPath, $theme);
            $asseticConfig = array_merge($asseticConfig, $themeConfig);
        }

        $container->getDefinition('assetic.config_resource')->replaceArgument(0, $asseticConfig);
    }

    protected function getAsseticThemeConfig($lessPath, ThemeInterface $theme)
    {
        $themeConfig = array();

        $themeConfig['theme_' . $theme->getName()] = array(
            array($lessPath . '/layout.less'),
            array('less'),
            array('output' => 'themes/' . $theme->getName() . '/style.css'),
        );

        return $themeConfig;
    }

    protected function getThemeVariables(ThemeInterface $theme)
    {
        $variables = array();

        if ($theme->getBodyBackground() !== '') {
            $variables['body-bg'] = $theme->getBodyBackground();
        }

        if ($theme->getPrimaryColor() !== '') {
            $variables['brand-primary'] = $theme->getPrimaryColor();
        }

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
        $rootPath = $container->getParameter('kernel.root_dir') . '/../';
        $bootstrapDirectory = 'vendor/twitter/bootstrap/less';
        $bootstrapFilepath = $rootPath . $bootstrapDirectory . '/bootstrap.less';

        $pattern = '/@import\s"([^"]+)";/';
        $imports = array();

        if (false === $count = preg_match_all($pattern, file_get_contents($bootstrapFilepath), $matches)) {
            throw new \RuntimeException('preg_match_all encountered an error');
        }

        $relativePath = '../../../../../' . $bootstrapDirectory;
        $template = "@import \"%s\";";
        for ($i = 0; $i < $count; $i++) {
            $filepath = $relativePath . '/' . $matches[1][$i];
            $imports[] = sprintf($template, $filepath);
        }

        $offset = array_search($relativePath . '/variables.less', $imports) + 1;
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
