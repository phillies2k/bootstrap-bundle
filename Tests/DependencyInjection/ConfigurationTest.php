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
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers P2\Bundle\BootstrapBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     * @group s
     */
    public function testGetConfigTreeBuilder()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(array()), array(array()));

        $this->assertArrayHasKey('use_forms', $config);
        $this->assertArrayHasKey('source_path', $config);
        $this->assertArrayHasKey('bootstrap_css', $config);
        $this->assertArrayHasKey('bootstrap_js', $config);
        $this->assertArrayHasKey('forms', $config);

        $this->assertTrue($config['use_forms']);
        $this->assertEquals('%kernel.root_dir%/../vendor/twbs/bootstrap', $config['source_path']);
        $this->assertEquals('css/bootstrap.css', $config['bootstrap_css']);
        $this->assertEquals('js/bootstrap.js', $config['bootstrap_js']);

        $forms = $config['forms'];

        $this->assertArrayHasKey('defaults', $forms);
        $this->assertArrayHasKey('allowed_types', $forms);
        $this->assertArrayHasKey('allowed_values', $forms);

        $this->assertInternalType('array', $forms['allowed_values']);

        $allowedTypes = $forms['allowed_types'];
        $this->assertArrayHasKey('prepend', $allowedTypes);
        $this->assertArrayHasKey('append', $allowedTypes);
        $this->assertArrayHasKey('horizontal', $allowedTypes);
        $this->assertArrayHasKey('inline', $allowedTypes);
        $this->assertArrayHasKey('grid', $allowedTypes);

        $this->assertEquals('bool', $allowedTypes['prepend']);
        $this->assertEquals('bool', $allowedTypes['append']);
        $this->assertEquals('bool', $allowedTypes['horizontal']);
        $this->assertEquals('bool', $allowedTypes['inline']);
        $this->assertEquals('array', $allowedTypes['grid']);

        $defaults = $forms['defaults'];

        $this->assertArrayHasKey('horizontal', $defaults);
        $this->assertArrayHasKey('inline', $defaults);
        $this->assertArrayHasKey('grid', $defaults);
        $this->assertTrue($defaults['horizontal']);
        $this->assertFalse($defaults['inline']);
        $this->assertInternalType('array', $defaults['grid']);
    }
}
