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

use P2\Bundle\BootstrapBundle\Form\AbstractTypeExtension;
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
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaults(OptionsResolverInterface $resolver)
    {
        return array('help' => null);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedTypes(OptionsResolverInterface $resolver)
    {
        return array('help' => array('null', 'string'));
    }

    /**
     * {@inheritDoc}
     */
    public function getViewVars(FormView $view, FormInterface $form, array $options)
    {
        return array('help' => $options['help']);
    }
}
