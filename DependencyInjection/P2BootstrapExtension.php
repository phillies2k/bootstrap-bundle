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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if ($config['use_forms'] === true) {
            $container->setParameter('p2_bootstrap.form.allowed_types', $config['forms']['allowed_types']);
            $container->setParameter('p2_bootstrap.form.allowed_values', $config['forms']['allowed_values']);
            $container->setParameter('p2_bootstrap.form.defaults', $config['forms']['defaults']);

            $loader->load('forms.yml');
        }
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
            $container->prependExtensionConfig(
                'assetic',
                array(
                    'assets' => $this->buildAsseticConfig($config, $container)
                )
            );
        }

        if (isset($bundles['TwigBundle']) && $config['use_forms'] === true) {
            $container->prependExtensionConfig(
                'twig',
                array(
                    'form' => array(
                        'resources' => array(
                            'P2BootstrapBundle::form_div_layout.html.twig'
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
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function buildAsseticConfig(array $config, ContainerBuilder $container)
    {
        $assets = array();

        $this->buildAsseticBootstrapCssConfig($config, $assets);
        $this->buildAsseticBootstrapJsConfig($config, $assets);
        $this->buildAsseticFontConfig($config, $container, $assets);

        return $assets;
    }

    /**
     * @param array $config
     * @param array $assets
     */
    protected function buildAsseticBootstrapCssConfig(array $config, array & $assets)
    {
        $assets['bootstrap_css'] = array(
            'inputs' => array($config['source_path'] . '/less/bootstrap.less'),
            'output' => $config['bootstrap_css']
        );
    }

    /**
     * @param array $config
     * @param array $assets
     */
    protected function buildAsseticBootstrapJsConfig(array $config, array & $assets)
    {
        $assets['bootstrap_js'] = array(
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
            'output' => $config['bootstrap_js']
        );
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param array $assets
     */
    protected function buildAsseticFontConfig(array $config, ContainerBuilder $container, array & $assets)
    {
        $fontPath = $container->getParameterBag()->resolveValue($config['source_path']) . '/fonts';

        foreach (glob($fontPath . '/*') as $path) {
            $fontName = preg_replace('/[.-]/', '_', basename($path));
            $assets[$fontName] = array(
                'inputs' => array($config['source_path'] . '/fonts/' . basename($path)),
                'output' => 'fonts/' . basename($path)
            );
        }
    }
}
