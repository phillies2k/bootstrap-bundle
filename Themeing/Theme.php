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

/**
 * Class Theme
 * @package P2\Bundle\BootstrapBundle\Themeing
 */
abstract class Theme implements ThemeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getBrandPrimary()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getBrandSuccess()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getBrandWarning()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getBrandDanger()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getBrandInfo()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getBodyBackground()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getTextColor()
    {
        return '';
    }

    /**
     * Returns the link color value for this theme.
     *
     * @return string
     */
    public function getLinkColor()
    {
        return '';
    }

    /**
     * Returns the link hover color value for this theme.
     *
     * @return string
     */
    public function getLinkHoverColor()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonDefaultColor()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonDefaultBackground()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonDefaultBorder()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonPrimaryColor()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonSuccessColor()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonWarningColor()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonDangerColor()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function getButtonInfoColor()
    {
        return '';
    }
}
