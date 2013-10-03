<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Interface TypeExtensionInterface
 * @package P2\Bundle\BootstrapBundle\Form
 */
interface TypeExtensionInterface
{
    /**
     * @param OptionsResolverInterface $resolver
     *
     * @return array
     */
    public function getDefaults(OptionsResolverInterface $resolver);

    /**
     * @param OptionsResolverInterface $resolver
     *
     * @return array
     */
    public function getAllowedTypes(OptionsResolverInterface $resolver);

    /**
     * @param OptionsResolverInterface $resolver
     *
     * @return array
     */
    public function getAllowedValues(OptionsResolverInterface $resolver);

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     *
     * @return array
     */
    public function getViewVars(FormView $view, FormInterface $form, array $options);
}
