<?php
/**
 * This file is part of the BootstrapBundle project.
 *
 * (c) 2013 Philipp Boes <mostgreedy@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace P2\Bundle\BootstrapBundle\Command;

use P2\Bundle\BootstrapBundle\Themeing\ThemeBuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateThemeCommand
 * @package P2\Bundle\BootstrapBundle\Command
 */
class GenerateThemeCommand extends Command
{
    /**
     * @var ThemeBuilderInterface
     */
    protected $themeBuilder;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('bootstrap:generate:theme')
            ->setDescription('Generates bootstrap themes')
            ->setHelp('no help available.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->getThemeBuilder()->buildThemes();

            return 0;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            return -1;
        }
    }

    /**
     * @param \P2\Bundle\BootstrapBundle\Themeing\ThemeBuilderInterface $themeBuilder
     *
     * @return GenerateThemeCommand
     */
    public function setThemeBuilder(ThemeBuilderInterface $themeBuilder)
    {
        $this->themeBuilder = $themeBuilder;

        return $this;
    }

    /**
     * @return \P2\Bundle\BootstrapBundle\Themeing\ThemeBuilderInterface
     */
    protected function getThemeBuilder()
    {
        return $this->themeBuilder;
    }
}
