<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace P2\Bundle\BootstrapBundle\Tests\Themeing\Theme;

/**
 * UnitTest ThemeTest
 * @package P2\Bundle\BootstrapBundle\Tests\Themeing\Theme
 */
class ThemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getBrandPrimary
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getBrandSuccess
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getBrandWarning
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getBrandDanger
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getBrandInfo
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getBodyBackground
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getTextColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getLinkColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getLinkHoverColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonDefaultColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonDefaultBackground
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonDefaultBorder
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonPrimaryColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonSuccessColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonWarningColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonDangerColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getButtonInfoColor
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getCustomVariables
     * @covers P2\Bundle\BootstrapBundle\Themeing\Theme::getName
     * @group s
     */
    public function testGetter()
    {
        $mock = $this->getMockForAbstractClass('P2\Bundle\BootstrapBundle\Themeing\Theme\Theme');
        $mock->expects($this->any())->method('getName')->will($this->returnValue('theme'));

        $this->assertEquals('', $mock->getBrandPrimary());
        $this->assertEquals('', $mock->getBrandSuccess());
        $this->assertEquals('', $mock->getBrandWarning());
        $this->assertEquals('', $mock->getBrandDanger());
        $this->assertEquals('', $mock->getBrandInfo());
        $this->assertEquals('', $mock->getBodyBackground());
        $this->assertEquals('', $mock->getTextColor());
        $this->assertEquals('', $mock->getLinkColor());
        $this->assertEquals('', $mock->getLinkHoverColor());
        $this->assertEquals('', $mock->getButtonDefaultColor());
        $this->assertEquals('', $mock->getButtonDefaultBackground());
        $this->assertEquals('', $mock->getButtonDefaultBorder());
        $this->assertEquals('', $mock->getButtonPrimaryColor());
        $this->assertEquals('', $mock->getButtonSuccessColor());
        $this->assertEquals('', $mock->getButtonWarningColor());
        $this->assertEquals('', $mock->getButtonDangerColor());
        $this->assertEquals('', $mock->getButtonInfoColor());
        $this->assertEquals(array(), $mock->getCustomVariables());
        $this->assertEquals('theme', $mock->getName());
    }
}
