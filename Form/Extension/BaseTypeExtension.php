<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BaseTypeExtension
 * @package P2\Bundle\BootstrapBundle\Form\Extension
 */
abstract class BaseTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['horizontal'] = $options['horizontal'];
        $view->vars['inline'] = $options['inline'];
        $view->vars['grid'] = $options['grid'];
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'horizontal' => true,
                'inline' => false,
                'grid' => array('sm' => array(4,8)),
            )
        );

        $resolver->setAllowedTypes(array('horizontal' => 'bool', 'inline' => 'bool', 'grid' => 'array'));
    }
}
