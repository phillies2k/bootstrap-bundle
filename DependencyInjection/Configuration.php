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
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('use_themes')->defaultTrue()->end()
                ->booleanNode('use_forms')->defaultTrue()->end()
                ->booleanNode('use_extensions')->defaultTrue()->end()
                ->scalarNode('public_path')->defaultValue('%kernel.root_dir%/../web/themes')->end()
                ->scalarNode('jquery_path')->defaultValue('%kernel.root_dir%/../components/jquery/jquery.js')->end()
                ->scalarNode('source_path')->defaultValue('%kernel.root_dir%/../vendor/twbs/bootstrap')->end()
                ->scalarNode('themes_path')->defaultValue('%kernel.root_dir%/Resources/themes')->end()
                ->scalarNode('bootstrap_css')->defaultValue('css/bootstrap.css')->end()
                ->scalarNode('bootstrap_js')->defaultValue('js/bootstrap.js')->end()
                ->scalarNode('jquery_js')->defaultValue('js/jquery.js')->end()
                ->scalarNode('less_path')->defaultNull()->end()
                ->arrayNode('forms')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('defaults')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('icon')->defaultNull()->end()
                                ->booleanNode('info')->defaultNull()->end()
                                ->booleanNode('help')->defaultNull()->end()
                                ->booleanNode('prepend')->defaultTrue()->end()
                                ->booleanNode('append')->defaultFalse()->end()
                                ->booleanNode('horizontal')->defaultTrue()->end()
                                ->booleanNode('inline')->defaultFalse()->end()
                                ->arrayNode('grid')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('xs')->prototype('integer')->end()->end()
                                        ->arrayNode('sm')->prototype('integer')->end()->end()
                                        ->arrayNode('md')->prototype('integer')->end()->end()
                                        ->arrayNode('lg')->prototype('integer')->end()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('allowed_types')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('icon')->defaultValue(array('null', 'string'))->end()
                                ->scalarNode('info')->defaultValue(array('null', 'string'))->end()
                                ->scalarNode('help')->defaultValue(array('null', 'string'))->end()
                                ->scalarNode('prepend')->defaultValue('bool')->end()
                                ->scalarNode('append')->defaultValue('bool')->end()
                                ->scalarNode('horizontal')->defaultValue('bool')->end()
                                ->scalarNode('inline')->defaultValue('bool')->end()
                                ->scalarNode('grid')->defaultValue('array')->end()
                            ->end()
                        ->end()
                        ->arrayNode('allowed_values')
                            ->addDefaultsIfNotSet()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
