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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateThemeCommand
 * @package P2\Bundle\BootstrapBundle\Command
 */
class GenerateThemeCommand extends ContainerAwareCommand
{
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

            $output->writeln("<notice>Themes build successfully!</notice>");

            return 0;
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");

            return -1;
        }
    }

    /**
     * Returns the theme builder service.
     *
     * @return \P2\Bundle\BootstrapBundle\Themeing\ThemeBuilderInterface
     * @throws \RuntimeException When the theme builder service was not found.
     */
    protected function getThemeBuilder()
    {
        if (! $this->getContainer()->has('p2_bootstrap.theme_builder')) {
            throw new \RuntimeException('Missing theme builder service definition.');
        }

        return $this->getContainer()->get('p2_bootstrap.theme_builder');
    }
}