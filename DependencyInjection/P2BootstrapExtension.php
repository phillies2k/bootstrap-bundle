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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class P2BootstrapExtension
 * @package P2\Bundle\BootstrapBundle\DependencyInjection
 */
class P2BootstrapExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('p2_bootstrap.source_directory', $config['source_path']);
        $container->setParameter('p2_bootstrap.themes_directory', $config['themes_path']);
        $container->setParameter('p2_bootstrap.themes', $config['themes']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['AsseticBundle'])) {
            $container->prependExtensionConfig('assetic', $this->buildAsseticAssetsConfig($config));
        }

        if (isset($bundles['TwigBundle'])) {
            $container->prependExtensionConfig(
                'twig',
                array(
                    'form' => array(
                        'resources' => array(
                            'P2BootstrapBundle::forms.html.twig'
                        )
                    )
                )
            );
        }
    }

    /**
     * Returns the assetic assets configuration section.
     *
     * @param array $config
     *
     * @return array
     */
    protected function buildAsseticAssetsConfig(array $config)
    {
        $assets = array(
            'jquery_js' => $this->buildAsseticJqueryConfig($config),
            'bootstrap_css' => $this->buildAsseticBootstrapCssConfig($config),
            'bootstrap_js' => $this->buildAsseticBootstrapJsConfig($config),
            'holder_js' => $this->buildAsseticHolderConfig($config),
        );

        $filters = array(
            'less' => null,
            'yui_js' => array(
                'jar' => __DIR__ . '/../Resources/java/yuicompressor.jar'
            )
        );

        foreach ($config['themes'] as $theme) {
            $assets['theme_' . $theme] = array(
                'inputs' => array($config['themes_path'] . '/' . $theme . '/less/layout.less'),
                'filters' => array('less'),
                'output' => $config['public_path'] . '/' . $theme . '/css/style.less'
            );
        }

        return array(
            'filters' => $filters,
            'assets' => $assets
        );
    }

    /**
     * Returns the assetic jquery configuration.
     *
     * @param array $config
     *
     * @return array
     */
    protected function buildAsseticJqueryConfig(array $config)
    {
        return array(
            'inputs' => array($config['jquery_path']),
            'output' => $config['jquery_js']
        );
    }

    /**
     * Returns the assetic holder js configuration.
     *
     * @param array $config
     *
     * @return array
     */
    protected function buildAsseticHolderConfig(array $config)
    {
        return array(
            'inputs' => array($config['source_path'] . '/assets/js/holder.js'),
            'output' => $config['holder_js']
        );
    }

    /**
     * Returns the assetic bootstrap css configuration.
     *
     * @param array $config
     *
     * @return array
     */
    protected function buildAsseticBootstrapCssConfig(array $config)
    {
        return array(
            'inputs' => array($config['source_path'] . '/less/bootstrap.less'),
            'filters' => array('less'),
            'output' => $config['bootstrap_css']
        );
    }

    /**
     * Returns the assetic bootstrap js configuration.
     *
     * @param array $config
     *
     * @return array
     */
    protected function buildAsseticBootstrapJsConfig(array $config)
    {
        return array(
            'inputs' => array(
                $config['source_path'] . '/js/transition.js',
                $config['source_path'] . '/js/alert.js',
                $config['source_path'] . '/js/button.js',
                $config['source_path'] . '/js/carousel.js',
                $config['source_path'] . '/js/collapse.js',
                $config['source_path'] . '/js/dropdown.js',
                $config['source_path'] . '/js/modal.js',
                $config['source_path'] . '/js/tooltip.js',
                $config['source_path'] . '/js/popover.js',
                $config['source_path'] . '/js/scrollspy.js',
                $config['source_path'] . '/js/tab.js',
                $config['source_path'] . '/js/affix.js'
            ),
            'filters' => array('yui_js'),
            'output' => $config['bootstrap_js']
        );
    }
}
