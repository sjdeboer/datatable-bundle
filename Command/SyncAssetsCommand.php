<?php
namespace Sjdeboer\DataTableBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SyncCommand
 * @package Sjdeboer\DataTableBundle\Command
 */
class SyncAssetsCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setName('sdeboer:datatable:sync-assets')
            ->setDescription('Installs DataTableBundle web assets under a public directory');
    }

    /**
     * @param $src
     * @param $dst
     */
    private function copy($src, $dst)
    {
        if (is_file($src)) {
            copy($src, $dst);
            return;
        }

        if (!file_exists($dst) || !is_dir($dst)) {
            mkdir($dst);
        }

        $files = scandir($src);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $this->copy($src . '/' . $file, $dst . '/' . $file);
        }
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $bundleDir = realpath(__DIR__ . '/..');
        $datatablesDir = $bundleDir . '/node_modules/datatables/media';

        if (!file_exists($datatablesDir) || !is_dir($datatablesDir)) {
            $output->writeln('Directory not found: ' . $datatablesDir);
            $output->writeln('Please navigate to DataTableBundle directory (' . $bundleDir . ') and run \'yarn install\'');
            return;
        }

        $this->copy($datatablesDir, $bundleDir . '/Resources/public');

        $command = $this->getApplication()->find('assets:install');
        $command->run(new ArrayInput([
            'command' => 'assets:install',
            'target' => 'public',
            '--symlink' => true,
        ]), $output);
    }
}
