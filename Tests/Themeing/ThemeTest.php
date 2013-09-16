<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\BootstrapBundle\Tests\Themeing;


/**
 * UnitTest ThemeTest
 * @package P2\Bundle\BootstrapBundle\Tests\Themeing
 */
class ThemeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up
     */
    protected function setUp()
    {
        parent::setUp();
    }
    
    /**
     * tear down
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @covers P2\Bundle\BootstrapBundle\Themeing::getPrimaryColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getSuccessColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getWarningColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getDangerColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getInfoColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getBodyBackground
     * @covers P2\Bundle\BootstrapBundle\Themeing::getTextColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonDefaultColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonDefaultBackground
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonDefaultBorder
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonPrimaryColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonSuccessColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonWarningColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonDangerColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getButtonInfoColor
     * @covers P2\Bundle\BootstrapBundle\Themeing::getName
     * @group s
     */
    public function testGetter()
    {
        $mock = $this->getMockForAbstractClass('P2\Bundle\BootstrapBundle\Themeing\Theme');
        $mock->expects($this->any())->method('getName')->will($this->returnValue('theme'));

        $this->assertEquals('', $mock->getPrimaryColor());
        $this->assertEquals('', $mock->getSuccessColor());
        $this->assertEquals('', $mock->getWarningColor());
        $this->assertEquals('', $mock->getDangerColor());
        $this->assertEquals('', $mock->getInfoColor());
        $this->assertEquals('', $mock->getBodyBackground());
        $this->assertEquals('', $mock->getTextColor());
        $this->assertEquals('', $mock->getButtonDefaultColor());
        $this->assertEquals('', $mock->getButtonDefaultBackground());
        $this->assertEquals('', $mock->getButtonDefaultBorder());
        $this->assertEquals('', $mock->getButtonPrimaryColor());
        $this->assertEquals('', $mock->getButtonSuccessColor());
        $this->assertEquals('', $mock->getButtonWarningColor());
        $this->assertEquals('', $mock->getButtonDangerColor());
        $this->assertEquals('', $mock->getButtonInfoColor());
        $this->assertEquals('theme', $mock->getName());
    }
}
