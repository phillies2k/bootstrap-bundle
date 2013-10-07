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

        if ($config['use_themes'] === true) {
            $container->setParameter('p2_bootstrap.source_directory', $config['source_path']);
            $container->setParameter('p2_bootstrap.themes_directory', $config['themes_path']);

            $loader->load('themeing.yml');
        }

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
            $container->prependExtensionConfig('assetic', $this->buildAsseticConfig($config, $container));
        }

        if (isset($bundles['TwigBundle']) && $config['use_forms'] === true) {
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
     * @param ContainerBuilder $container
     *
     * @return array
     */
    protected function buildAsseticConfig(array $config, ContainerBuilder $container)
    {
        $assets = array();

        $this->buildAsseticBootstrapCssConfig($config, $assets);
        $this->buildAsseticBootstrapJsConfig($config, $assets);
        $this->buildAsseticJqueryConfig($config, $assets);

        $this->buildAsseticFontConfig($config, $container, $assets);
        $this->buildAsseticThemeConfig($config, $container, $assets);

        return array(
            'assets' => $assets
        );
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
     * @param array $assets
     */
    protected function buildAsseticJqueryConfig(array $config, array & $assets)
    {
        $assets['jquery_js'] = array(
            'inputs' => array($config['jquery_path']),
            'output' => $config['jquery_js']
        );
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param array $assets
     */
    protected function buildAsseticFontConfig(array $config, ContainerBuilder $container, array & $assets)
    {
        $fontPattern = $container->getParameterBag()->resolveValue($config['source_path']) . '/fonts/*';
        $webPath = $container->getParameter('kernel.root_dir') . '/../web';

        foreach (glob($fontPattern) as $fontPath) {
            $fontName = basename($fontPath);
            $assets[$fontName] = array(
                'inputs' => array($fontPath),
                'output' => $webPath . '/fonts/' . $fontName
            );
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param array $assets
     */
    protected function buildAsseticThemeConfig(array $config, ContainerBuilder $container, array & $assets)
    {
        $webPath = $container->getParameter('kernel.root_dir') . '/../web/';
        $publicPath = $webPath . ltrim($config['public_path'], '/');

        $themesPath = $container->getParameterBag()->resolveValue($config['themes_path']);
        $stylePath = 'less/layout/style.less';

        foreach (glob($themesPath . '/*/' . $stylePath) as $filepath) {
            if (false !== preg_match('#(\w+)/' . $stylePath . '#', $filepath, $matches)) {
                $theme = $matches[1];

                $assets[$theme . '_style'] = array(
                    'inputs' => array($filepath),
                    'output' => $publicPath . '/' . $theme . '/css/style.css'
                );
            }
        }
    }
}
