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
 * Class FormTypeExtension
 * @package P2\Bundle\BootstrapBundle\Form\Extension
 */
class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['horizontal'] = $options['horizontal'];
        $view->vars['inline'] = $options['inline'];
        $view->vars['grid'] = $options['grid'];
        $view->vars['help'] = $options['help'];
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
                'help' => null,
            )
        );

        $resolver->setAllowedValues(
            array(
                'horizontal' => array(true, false),
                'inline' => array(true, false)
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
