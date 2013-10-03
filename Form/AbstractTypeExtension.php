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

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Form\AbstractTypeExtension as BaseTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractTypeExtension
 * @package P2\Bundle\BootstrapBundle\Form\Extension
 */
abstract class AbstractTypeExtension extends BaseTypeExtension implements TypeExtensionInterface
{
    /**
     * @var array
     */
    protected $allowedTypes = array();

    /**
     * @var array
     */
    protected $allowedValues = array();

    /**
     * @var array
     */
    protected $defaults = array();

    /**
     * Sets an array of allowed types.
     *
     * @param array $allowedTypes
     * @return $this
     */
    public function setAllowedTypes(array $allowedTypes)
    {
        $this->allowedTypes = array_merge($this->allowedTypes, $allowedTypes);

        return $this;
    }

    /**
     * @param array $allowedValues
     *
     * @return $this
     */
    public function setAllowedValues(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;

        return $this;
    }

    /**
     * Sets an array of default options for this form type extension.
     *
     * @param array $defaults
     * @return $this
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = array_merge($this->defaults, $defaults);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        foreach (array_keys($this->defaults) as $key) {
            if (isset($options[$key])) {
                $view->vars[$key] = $options[$key];
            }
        }

        foreach ($this->getViewVars($view, $form, $options) as $key => $value) {
            $view->vars[$key] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array_merge($this->defaults, $this->getDefaults($resolver)));
        $resolver->setAllowedTypes(array_merge($this->allowedTypes, $this->getAllowedTypes($resolver)));
        $resolver->setAllowedValues(array_merge($this->allowedValues, $this->getAllowedValues($resolver)));
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaults(OptionsResolverInterface $resolver)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedTypes(OptionsResolverInterface $resolver)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedValues(OptionsResolverInterface $resolver)
    {
        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function getViewVars(FormView $view, FormInterface $form, array $options)
    {
        return array();
    }
}
