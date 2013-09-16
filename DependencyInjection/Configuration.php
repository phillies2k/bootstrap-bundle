<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package P2\Bundle\BootstrapBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('p2_bootstrap');

        $rootNode
            ->children()
                ->scalarNode('theme_path')->defaultValue('%kernel.root_dir%/Resources/themes')->end()
                ->scalarNode('public_path')->defaultValue('themes')->end()
                ->scalarNode('bootstrap_css')->defaultValue('css/bootstrap.css')->end()
                ->scalarNode('bootstrap_js')->defaultValue('js/bootstrap.js')->end()
                ->scalarNode('jquery')->defaultValue('js/jquery.js')->end()
                ->scalarNode('holder')->defaultValue('js/holder.js')->end()
                ->scalarNode('path_jquery')->defaultValue('%kernel.root_dir%/../components/jquery')->end()
                ->scalarNode('path_bootstrap')->defaultValue('%kernel.root_dir%/../vendor/twitter/bootstrap')->end()
            ->end();

        return $treeBuilder;
    }
}
