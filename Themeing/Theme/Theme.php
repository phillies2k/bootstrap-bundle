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
 * Class Theme
 * @package P2\Bundle\BootstrapBundle\Themeing\Theme
 */
abstract class Theme implements ThemeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCustomVariables()
    {
        return array();
    }

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
     * {@inheritDoc}
     */
    public function getLinkColor()
    {
        return '';
    }

    /**
     * {@inheritDoc}
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