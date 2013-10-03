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

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use P2\Bundle\BootstrapBundle\Themeing\ThemeBuilder;

/**
 * UnitTest ThemeBuilderTest
 * @package P2\Bundle\BootstrapBundle\Tests\Themeing
 */
class ThemeBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ThemeBuilder
     */
    protected $themeBuilder;

    /**
     * @var vfsStreamDirectory
     */
    protected $themesDirectory;

    /**
     * @var vfsStreamDirectory
     */
    protected $sourceDirectory;

    /**
     * set up
     */
    protected function setUp()
    {
        parent::setUp();

        $structure = array('less' => array('variables.less' => '// vars'));
        $this->sourceDirectory = vfsStream::setup('source', null, $structure);
        $this->themesDirectory = vfsStream::setup('themes');
        $this->themeBuilder = new ThemeBuilder(vfsStream::url('source'), vfsStream::url('themes'));
    }
    
    /**
     * tear down
     */
    protected function tearDown()
    {
        $this->sourceDirectory = null;
        $this->themesDirectory = null;
        $this->themeBuilder = null;

        parent::tearDown();
    }

    /**
     * @covers P2\Bundle\BootstrapBundle\Themeing\ThemeBuilder::__construct
     * @group s
     */
    public function testConstruct()
    {
        $builder = new ThemeBuilder('source/', 'themes/');

        $sourceDirectoryReflection = new \ReflectionProperty($builder, 'sourceDirectory');
        $themesDirectoryReflection = new \ReflectionProperty($builder, 'themesDirectory');
        $themesReflection = new \ReflectionProperty($builder, 'themes');

        $this->assertTrue($sourceDirectoryReflection->isProtected());
        $this->assertTrue($themesDirectoryReflection->isProtected());
        $this->assertTrue($themesReflection->isProtected());

        $sourceDirectoryReflection->setAccessible(true);
        $themesDirectoryReflection->setAccessible(true);
        $themesReflection->setAccessible(true);

        $sourceDirectoryValue = $sourceDirectoryReflection->getValue($builder);
        $themesDirectoryValue = $themesDirectoryReflection->getValue($builder);
        $themesValue = $themesReflection->getValue($builder);

        $this->assertEquals('source/', $sourceDirectoryValue);
        $this->assertEquals('themes/', $themesDirectoryValue);
        $this->assertEquals(array(), $themesValue);
    }

    /**
     * @covers P2\Bundle\BootstrapBundle\Themeing\ThemeBuilder::addTheme
     * @group s
     */
    public function testAddTheme()
    {
        $themeMock = $this->getMockForAbstractClass('P2\Bundle\BootstrapBundle\Themeing\Theme\Theme');
        $themeMock->expects($this->any())->method('getName')->will($this->returnValue('default'));
        $propertyReflection = new \ReflectionProperty($this->themeBuilder, 'themes');
        $this->assertTrue($propertyReflection->isProtected());
        $propertyReflection->setAccessible(true);
        $this->assertEquals(array(), $propertyReflection->getValue($this->themeBuilder));
        $this->themeBuilder->addTheme($themeMock);
        $value = $propertyReflection->getValue($this->themeBuilder);
        $this->assertCount(1, $value);
        $this->assertArrayHasKey('default', $value);
        $this->assertInstanceOf('P2\Bundle\BootstrapBundle\Themeing\Theme\ThemeInterface', array_shift($value));
    }

    /**
     * @covers P2\Bundle\BootstrapBundle\Themeing\ThemeBuilder::buildThemes
     * @group s
     */
    public function testBuildThemes()
    {
        $this->markTestSkipped('Skipped for now, due to a strange vfs stream error.');

        $themeMock = $this->getMockForAbstractClass('P2\Bundle\BootstrapBundle\Themeing\Theme\Theme');
        $themeMock->expects($this->any())->method('getName')->will($this->returnValue('default'));
        $this->themeBuilder->addTheme($themeMock);
        $this->assertFalse($this->themesDirectory->hasChild('default'));
        $this->themeBuilder->buildThemes();
        $this->assertTrue($this->themesDirectory->hasChild('default'));
    }
}
