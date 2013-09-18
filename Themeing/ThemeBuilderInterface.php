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
     * Adds a theme to the theme builder.
     *
     * @param ThemeInterface $theme
     *
     * @return ThemeBuilderInterface
     */
    public function addTheme(ThemeInterface $theme);

    /**
     * Builds the themes.
     *
     * @return void
     */
    public function buildThemes();
}
