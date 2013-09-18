<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\BootstrapBundle\Tests\DependencyInjection;
use P2\Bundle\BootstrapBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;


/**
 * UnitTest ConfigurationTest
 * @package P2\Bundle\BootstrapBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase {
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
     * @covers P2\Bundle\BootstrapBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @group s
     */
    public function testGetConfigTreeBuilder()
    {
        $processor = new Processor();
        $configuration = new Configuration(array());
        $config = $processor->processConfiguration($configuration, array(array()));

        $this->assertArrayHasKey('jquery_path', $config);
        $this->assertArrayHasKey('source_path', $config);
        $this->assertArrayHasKey('themes_path', $config);
        $this->assertArrayHasKey('public_path', $config);
        $this->assertArrayHasKey('bootstrap_css', $config);
        $this->assertArrayHasKey('bootstrap_js', $config);
        $this->assertArrayHasKey('jquery_js', $config);
        $this->assertArrayHasKey('holder_js', $config);
    }
}
