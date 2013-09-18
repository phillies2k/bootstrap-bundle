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
 * Interface ThemeBuilderInterface
 * @package P2\Bundle\BootstrapBundle\Themeing
 */
interface ThemeBuilderInterface 
{
    /**
     * Creates the given theme.
     *
     * @param ThemeInterface $theme
     *
     * @return void
     */
    public function buildTheme(ThemeInterface $theme);

    /**
     * Returns an array of built themes.
     *
     * @return ThemeInterface[]
     */
    public function getThemes();
}
