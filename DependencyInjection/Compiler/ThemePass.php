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

/**
 * Class ThemePass
 * @package P2\Bundle\BootstrapBundle\DependencyInjection\Compiler
 */
class ThemePass implements CompilerPassInterface
{
    /**
     * Template for layout.less file
     *
     * @var string
     */
    const LESS_LAYOUT = <<<LESS_LAYOUT
// Theme: %theme%
//
// Imports
// -------------------------------------------------
@import "bootstrap.less";

// Put your custom styles here

LESS_LAYOUT;

    /**
     * Template for variables.less file
     *
     * @var string
     */
    const LESS_VARIABLES = <<<LESS_VARIABLES
// Theme: %theme%
//
// Variables
// -------------------------------------------------

%contents%

LESS_VARIABLES;

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('assetic.config_resource')) {
            throw new \RuntimeException('Missing assetic bundle.');
        }

        $asseticConfig = $container->getDefinition('assetic.config_resource')->getArgument(0);

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $container->getExtensionConfig('p2_bootstrap'));

        foreach ($container->findTaggedServiceIds('bootstrap.theme') as $id => $attributes) {
            $theme = $container->get($id);

            if ($theme instanceof ThemeInterface) {
                $this->buildThemeFiles($config, $container, $theme);
                $this->symlinkFonts($config, $container, $theme);

                $themeConfig = $this->buildAsseticThemeConfig($config, $container, $theme);
                $asseticConfig = array_merge($asseticConfig, $themeConfig);
            }
        }

        $container->getDefinition('assetic.config_resource')->replaceArgument(0, $asseticConfig);
    }

    /**
     * Creates the less files for the given theme.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     */
    protected function buildThemeFiles(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $path = $this->resolveThemePath($config['theme_path'], $container, $theme) . '/less';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if (! file_exists($path . '/bootstrap.less')) {
            file_put_contents($path . '/bootstrap.less', $this->generateBootstrapLess($config, $container));
        }

        if (! file_exists($path . '/layout.less')) {
            file_put_contents($path . '/layout.less', $this->generateLayoutLess($theme));
        }

        file_put_contents($path . '/variables.less', $this->generateVariablesLess($theme));
    }

    /**
     * Generates the bootstrap less file.
     *
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function generateBootstrapLess(array $config, ContainerBuilder $container)
    {
        $bootstrapDirectory = $container->getParameterBag()->resolveValue($config['path_bootstrap']);
        $bootstrapFilepath = $bootstrapDirectory . '/less/bootstrap.less';

        $pattern = '/@import\s"([^"]+)";/';
        $imports = array();

        if (false === $count = preg_match_all($pattern, file_get_contents($bootstrapFilepath), $matches)) {
            throw new \RuntimeException('preg_match_all encountered an error');
        }

        $relativePath = '../../../../../vendor/twitter/bootstrap/less';
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

    /**
     * Generates the layout less file for the given theme.
     *
     * @param ThemeInterface $theme
     *
     * @return string
     */
    protected function generateLayoutLess(ThemeInterface $theme)
    {
        return strtr(static::LESS_LAYOUT, array('%theme%' => $theme->getName()));
    }

    /**
     * Generates the variables less file for the given theme.
     *
     * @param ThemeInterface $theme
     *
     * @return string
     */
    protected function generateVariablesLess(ThemeInterface $theme)
    {
        $contents = "";
        foreach ($this->getThemeVariables($theme) as $name => $value) {
            $contents .= "@" . $name . ": " . $value . ";\n";
        }

        return strtr(static::LESS_VARIABLES, array('%theme%' => $theme->getName(), '%contents%' => $contents));
    }

    /**
     * Adds theme symlinks for bootstraps glyphicon font.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     */
    protected function symlinkFonts(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $pattern = $container->getParameterBag()->resolveValue($config['path_bootstrap']) . '/fonts/*';
        $rootPath = $container->getParameter('kernel.root_dir') . '/../web';
        $fontPath = $rootPath . '/' . $this->resolveThemePath($config['public_path'], $container, $theme) . '/fonts';

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
            array($this->resolveThemePath($config['theme_path'], $container, $theme) . '/less/layout.less'),
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
     * Returns present theme variables as an associative array.
     *
     * @param ThemeInterface $theme
     *
     * @return array
     */
    protected function getThemeVariables(ThemeInterface $theme)
    {
        $variables = array();

        if ($theme->getBrandPrimary() !== '') {
            $variables['brand-primary'] = $theme->getBrandPrimary();
        }

        if ($theme->getBrandPrimary() !== '') {
            $variables['brand-primary'] = $theme->getBrandPrimary();
        }

        if ($theme->getBrandSuccess() !== '') {
            $variables['brand-success'] = $theme->getBrandSuccess();
        }

        if ($theme->getBrandWarning() !== '') {
            $variables['brand-warning'] = $theme->getBrandWarning();
        }

        if ($theme->getBrandDanger() !== '') {
            $variables['brand-danger'] = $theme->getBrandDanger();
        }

        if ($theme->getBrandInfo() !== '') {
            $variables['brand-info'] = $theme->getBrandInfo();
        }

        if ($theme->getBodyBackground() !== '') {
            $variables['body-bg'] = $theme->getBodyBackground();
        }

        if ($theme->getTextColor() !== '') {
            $variables['text-color'] = $theme->getTextColor();
        }

        if ($theme->getLinkColor() !== '') {
            $variables['link-color'] = $theme->getLinkColor();
        }

        if ($theme->getLinkHoverColor() !== '') {
            $variables['link-hover-color'] = $theme->getLinkHoverColor();
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
}
