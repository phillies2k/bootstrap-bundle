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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $bundles = $container->getParameter('kernel.bundles');

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

        if (isset($bundles['AsseticBundle'])) {
            $container->prependExtensionConfig(
                'assetic',
                array(
                    'filters' => array(
                        'less' => null,
                        'yui_js' => array(
                            'jar' => __DIR__ . '/../Resources/java/yuicompressor.jar'
                        )
                    ),
                    'assets' => array(
                        'jquery' => $this->buildAsseticJqueryConfig($config),
                        'bootstrap_css' => $this->buildAsseticBootstrapCssConfig($config),
                        'bootstrap_js' => $this->buildAsseticBootstrapJsConfig($config),
                        'holder' => $this->buildAsseticHolderConfig($config),
                    )
                )
            );
        }
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
            'inputs' => array($config['path_jquery'] . '/jquery.min.js'),
            'output' => $config['jquery']
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
            'inputs' => array($config['path_bootstrap'] . '/assets/js/holder.js'),
            'output' => $config['holder']
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
            'inputs' => array($config['path_bootstrap'] . '/less/bootstrap.less'),
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
                $config['path_bootstrap'] . '/js/transition.js',
                $config['path_bootstrap'] . '/js/alert.js',
                $config['path_bootstrap'] . '/js/button.js',
                $config['path_bootstrap'] . '/js/carousel.js',
                $config['path_bootstrap'] . '/js/collapse.js',
                $config['path_bootstrap'] . '/js/dropdown.js',
                $config['path_bootstrap'] . '/js/modal.js',
                $config['path_bootstrap'] . '/js/tooltip.js',
                $config['path_bootstrap'] . '/js/popover.js',
                $config['path_bootstrap'] . '/js/scrollspy.js',
                $config['path_bootstrap'] . '/js/tab.js',
                $config['path_bootstrap'] . '/js/affix.js'
            ),
            'filters' => array('yui_js'),
            'output' => $config['bootstrap_js']
        );
    }
}
