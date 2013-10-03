<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\Themeing\Theme;

/**
 * Interface ThemeInterface
 * @package P2\Bundle\BootstrapBundle\Themeing\Theme
 */
interface ThemeInterface 
{
    /**
     * Returns the name of this theme.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns an array of custom variables for this theme.
     *
     * @return array
     */
    public function getCustomVariables();

    /**
     * Returns the primary color value for this theme.
     *
     * @return string
     */
    public function getBrandPrimary();

    /**
     * Returns the success color value for this theme.
     *
     * @return string
     */
    public function getBrandSuccess();

    /**
     * Returns the warning color value for this theme.
     *
     * @return string
     */
    public function getBrandWarning();

    /**
     * Returns the danger color value for this theme.
     *
     * @return string
     */
    public function getBrandDanger();

    /**
     * Returns the info color value for this theme.
     *
     * @return string
     */
    public function getBrandInfo();

    /**
     * Returns the body background value for this theme.
     *
     * @return string
     */
    public function getBodyBackground();

    /**
     * Returns the text color value for this theme.
     *
     * @return string
     */
    public function getTextColor();

    /**
     * Returns the link color value for this theme.
     *
     * @return string
     */
    public function getLinkColor();

    /**
     * Returns the link hover color value for this theme.
     *
     * @return string
     */
    public function getLinkHoverColor();

    /**
     * Returns the default button color value for this theme.
     *
     * @return string
     */
    public function getButtonDefaultColor();

    /**
     * Returns the default button background value for this theme.
     *
     * @return string
     */
    public function getButtonDefaultBackground();

    /**
     * Returns the default button border value for this theme.
     *
     * @return string
     */
    public function getButtonDefaultBorder();

    /**
     * Returns the primary button color value for this theme.
     *
     * @return string
     */
    public function getButtonPrimaryColor();

    /**
     * Returns the success button color value for this theme.
     *
     * @return string
     */
    public function getButtonSuccessColor();

    /**
     * Returns the warning button color value for this theme.
     *
     * @return string
     */
    public function getButtonWarningColor();

    /**
     * Returns the danger button color value for this theme.
     *
     * @return string
     */
    public function getButtonDangerColor();

    /**
     * Returns the info button color value for this theme.
     *
     * @return string
     */
    public function getButtonInfoColor();
}