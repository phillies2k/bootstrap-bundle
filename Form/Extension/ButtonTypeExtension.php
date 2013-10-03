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
 * Class ButtonTypeExtension
 * @package P2\Bundle\BootstrapBundle\Form\Extension
 */
class ButtonTypeExtension extends AbstractTypeExtension
{
    /**
     * default button
     */
    Const BUTTON_DEFAULT = 'default';

    /**
     * primary button
     */
    Const BUTTON_PRIMARY = 'primary';

    /**
     * success button
     */
    Const BUTTON_SUCCESS = 'success';

    /**
     * warning button
     */
    Const BUTTON_WARNING = 'warning';

    /**
     * danger button
     */
    Const BUTTON_DANGER = 'danger';

    /**
     * info button
     */
    Const BUTTON_INFO = 'info';

    /**
     * @var array
     */
    protected static $buttons = array(
        self::BUTTON_DEFAULT,
        self::BUTTON_DANGER,
        self::BUTTON_INFO,
        self::BUTTON_PRIMARY,
        self::BUTTON_SUCCESS,
        self::BUTTON_WARNING
    );

    /**
     * {@inheritDoc}
     */
    public function getViewVars(FormView $view, FormInterface $form, array $options)
    {
        return array('button' => $options['button'], 'icon' => $options['icon']);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaults(OptionsResolverInterface $resolver)
    {
        return array('button' => static::BUTTON_DEFAULT, 'icon' => null);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllowedValues(OptionsResolverInterface $resolver)
    {
        return array('button' => static::$buttons);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtendedType()
    {
        return 'button';
    }
}
