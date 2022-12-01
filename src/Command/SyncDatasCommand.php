<?php

namespace App\Command;

use App\ModulesLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:sync-datas')]
class SyncDatasCommand extends Command
{
    public function __construct(private readonly ModulesLoader $loader) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loader->sync();

        return Command::SUCCESS;
    }
}