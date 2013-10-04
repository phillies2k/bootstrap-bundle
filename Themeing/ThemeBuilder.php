<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\Themeing;

use P2\Bundle\BootstrapBundle\Themeing\Theme\ThemeInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class ThemeBuilder
 * @package P2\Bundle\BootstrapBundle\Themeing
 */
class ThemeBuilder implements ThemeBuilderInterface
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var string
     */
    protected $sourceDirectory;

    /**
     * @var string
     */
    protected $themesDirectory;

    /**
     * @var ThemeInterface[]
     */
    protected $themes;

    /**
     * @param EngineInterface $templating
     * @param string $sourceDirectory
     * @param string $themesDirectory
     */
    public function __construct(EngineInterface $templating, $sourceDirectory, $themesDirectory)
    {
        $this->templating = $templating;
        $this->sourceDirectory = $sourceDirectory;
        $this->themesDirectory = $themesDirectory;
        $this->themes = array();
    }

    /**
     * {@inheritDoc}
     */
    public function addTheme(ThemeInterface $theme)
    {
        $this->themes[$theme->getName()] = $theme;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function buildThemes()
    {
        foreach ($this->themes as $theme) {
            $path = $this->themesDirectory . '/' . $theme->getName() . '/less';

            if (! is_dir($path . '/layout')) {
                mkdir($path . '/layout', 0777, true);
            }

            // only create style.less if this file does not exists already (we do not want to overwrite custom styling)
            if (! file_exists($path . '/layout/style.less')) {
                $contents = $this->templating->render(
                    'P2BootstrapBundle::style.less.twig',
                    array(
                        'theme' => $theme->getName()
                    )
                );

                file_put_contents($path . '/layout/style.less', $contents);
            }

            $contents = $this->templating->render(
                'P2BootstrapBundle::theme.less.twig',
                array(
                    'variables' => $this->buildBootstrapVariables($theme),
                    'theme' => $theme->getName()
                )
            );

            file_put_contents($path . '/theme.less', $contents);
        }
    }

    /**
     * Returns an array of variables for this theme.
     *
     * @param ThemeInterface $theme
     *
     * @return array
     */
    protected function buildBootstrapVariables(ThemeInterface $theme)
    {
        $variables = $this->parseVariablesFromFile($this->sourceDirectory . '/less/variables.less');
        $variables['icon-font-path'] = '"../../../../fonts/"';

        foreach ($this->getThemeVariables($theme) as $name => $value) {
            $variables[$name] = $value;
        }

        return $variables;
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
            if (false !== preg_match('/^@([^:]+)\:([^;]+);/', $row, $matches)) {
                if (isset($matches[1])) {
                    $variables[$matches[1]] = trim($matches[2]);
                }
            }
        }

        return $variables;
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
