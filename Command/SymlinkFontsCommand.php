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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class SymlinkFontsCommand
 * @package P2\Bundle\BootstrapBundle\Command
 */
class SymlinkFontsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('bootstrap:symlink:fonts')
            ->setDescription('Symlink bootstrap fonts to the public web root.')
            ->setHelp('no help available.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $origin = $this->getContainer()->getParameter('p2_bootstrap.source_directory') . '/fonts';
            $target = $this->getContainer()->getParameter('kernel.root_dir') . '/../web/fonts';

            $filesystem = new Filesystem();

            if (! $filesystem->exists($origin)) {
                $output->writeln(sprintf("<error>Invalid source path: %s</error>", $origin));

                return -1;
            }

            if (! $filesystem->exists($target)) {
                $filesystem->symlink($origin, $target);

                $output->writeln(sprintf("<comment>symlink created:</comment> %s > %s", $origin, $target));
            }

            return 0;
        } catch (\Exception $e) {
            $output->writeln(sprintf("<error>%s</error>", $e->getMessage()));

            return -1;
        }
    }
}
