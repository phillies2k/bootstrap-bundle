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

        $resourcesConfig = $container->getDefinition('assetic.config_resource')->getArgument(0);
        $extensionConfig = $this->getExtensionConfiguration($container);

        foreach ($container->findTaggedServiceIds('bootstrap.theme') as $id => $attributes) {
            $theme = $container->get($id);

            if ($theme instanceof ThemeInterface) {
                $this->buildThemeFiles($extensionConfig, $container, $theme);
                $this->symlinkFonts($extensionConfig, $container, $theme);

                $themeConfig = $this->buildAsseticThemeConfig($extensionConfig, $container, $theme);
                $resourcesConfig = array_merge($resourcesConfig, $themeConfig);
            }
        }

        $container->getDefinition('assetic.config_resource')->replaceArgument(0, $resourcesConfig);
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

        file_put_contents($path . '/variables.less', $this->generateThemeVariablesLess($config, $container, $theme));
        file_put_contents($path . '/bootstrap.less', $this->generateBootstrapLess($config, $container, $theme));

        // only create layout.less if this file does not exists already (we do not want to overwrite custom styling)
        if (! file_exists($path . '/layout.less')) {
            file_put_contents($path . '/layout.less', $this->generateLayoutLess($theme));
        }
    }

    /**
     * Returns the variables.less stylesheet contents.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     *
     * @return string
     */
    protected function generateThemeVariablesLess(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $contents = "";

        foreach ($this->buildBootstrapVariables($config, $container, $theme) as $name => $value) {
            $contents .= "@" . $name . ": " . $value . ";\n";
        }

        return strtr(static::LESS_VARIABLES, array('%theme%' => $theme->getName(), '%contents%' => $contents));
    }

    /**
     * Returns an array of variables for this theme.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     *
     * @return array
     */
    protected function buildBootstrapVariables(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $variables = $this->parseBootstrapVariables($config, $container);

        foreach ($this->getThemeVariables($theme) as $name => $value) {
            $variables[$name] = $value;
        }

        return $variables;
    }

    /**
     * Parses variables values from the bootstrap variables.less file.
     *
     * @param array $config
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function parseBootstrapVariables(array $config, ContainerBuilder $container)
    {
        $source = $container->getParameterBag()->resolveValue($config['path_bootstrap_less']) . '/variables.less';

        return $this->parseVariablesFromFile($source);
    }

    /**
     * Parses variables from the given file path.
     *
     * @param string $filepath
     *
     * @return array
     */
    protected function parseVariablesFromFile($filepath)
    {
        $contents = file_get_contents($filepath);
        $contents = str_replace(array("\r", "\r\n"), "\n", $contents);
        $variables = array();
        $code = explode("\n", $contents);

        foreach ($code as $row) {
            if (false !== preg_match('/^@([^:]+)\:([^;])+;/', $row, $matches)) {
                if (isset($matches[1])) {
                    $variables[$matches[1]] = trim($matches[2]);
                }
            }
        }

        return $variables;
    }

    /**
     * Generates the bootstrap.less file for the given theme.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function generateBootstrapLess(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $relativePath = $this->getRelativeBootstrapPath($config, $container, $theme);
        $imports = $this->parseImports($config, $container);

        $template = "@import \"%s\";";
        for ($i = 0; $i < count($imports); $i++) {
            $filepath = $imports[$i];
            if ($imports[$i] !== 'variables.less') {
                $filepath = $relativePath . '/' . $filepath;
            }

            $imports[$i] = sprintf($template, $filepath);
        }

        $contents = "// This file is auto generated.\n\n";
        $contents.= implode("\n", $imports);

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
        $bootstrapDirectory = $container->getParameterBag()->resolveValue($config['path_bootstrap_less']);
        $bootstrapFilepath = $bootstrapDirectory . '/bootstrap.less';

        if (false === $count = preg_match_all('/@import\s"([^"]+)";/', file_get_contents($bootstrapFilepath), $matches)) {
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
     * @param ThemeInterface $theme
     *
     * @return string
     */
    protected function getRelativeBootstrapPath(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $bootstrapPath = $container->getParameterBag()->resolveValue($config['path_bootstrap_less']);
        $rootPath = $container->getParameter('kernel.root_dir') . '/../';

        return $this->getRelativeRootPath($config, $container, $theme) . substr($bootstrapPath, strlen($rootPath));
    }

    /**
     * Returns the relative path to the project root for the given theme.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     *
     * @return string
     */
    protected function getRelativeRootPath(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $themeDirectory = realpath($this->resolveThemePath($config['theme_path'], $container, $theme) . '/less');
        $rootDirectory = realpath($container->getParameter('kernel.root_dir') . '/..');
        $path = substr($themeDirectory, strlen($rootDirectory) + 1);
        $step = count(explode('/', $path));

        return str_repeat('../', $step);
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
     * Adds theme symlinks for bootstraps glyphicon font.
     *
     * @param array $config
     * @param ContainerBuilder $container
     * @param ThemeInterface $theme
     */
    protected function symlinkFonts(array $config, ContainerBuilder $container, ThemeInterface $theme)
    {
        $pattern = $container->getParameterBag()->resolveValue($config['path_bootstrap_fonts']) . '/*';
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

        foreach ($theme->getCustomVariables() as $name => $value) {
            $variables[$name] = $value;
        }

        return $variables;
    }
}
