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

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ThemeBuilder
 * @package P2\Bundle\BootstrapBundle\Themeing
 */
class ThemeBuilder implements ThemeBuilderInterface
{
    /**
     * Template for layout.less file
     *
     * @var string
     */
    const LESS_LAYOUT = <<<LESS_LAYOUT
//
// Theme: %theme%
//
// Layout
// -------------------------------------------------
@import "theme.less";

// Put your custom styles here

LESS_LAYOUT;

    /**
     * Template for theme.less file
     *
     * @var string
     */
    const LESS_THEME = <<<LESS_THEME
//
// Theme: %theme%
// Last-Modified: %datetime%
//
// This file is auto-generated.
// -------------------------------------------------
//

%contents%

@import "../../bootstrap.less";

LESS_THEME;

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
     * @param string $sourceDirectory
     * @param string $themesDirectory
     */
    public function __construct($sourceDirectory, $themesDirectory)
    {
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

            if (! is_dir($path)) {
                mkdir($path, 0777, true);
            }

            // only create layout.less if this file does not exists already (we do not want to overwrite custom styling)
            if (! file_exists($path . '/layout.less')) {
                file_put_contents($path . '/layout.less', $this->generateLayoutLess($theme));
            }

            file_put_contents($path . '/theme.less', $this->generateThemeLess($theme));
        }
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
     * Returns the theme.less stylesheet contents.
     *
     * @param ThemeInterface $theme
     *
     * @return string
     */
    protected function generateThemeLess(ThemeInterface $theme)
    {
        $contents = "";

        foreach ($this->buildBootstrapVariables($theme) as $name => $value) {
            $contents .= "@" . $name . ": " . $value . ";\n";
        }

        return strtr(
            static::LESS_THEME,
            array(
                '%theme%' => $theme->getName(),
                '%datetime%' => date('d/m/Y H:i:s', time()),
                '%contents%' => $contents
            )
        );
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
        $variables['icon-font-path'] = '"../../../fonts/"';

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
