<?php

namespace App\Command;

use App\Modules\Netatmo\NetatmoApi;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:netatmo:fetch-data')]
class FetchNetatmoDataCommand extends Command
{
    public function __construct(private readonly NetatmoApi $api)
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump($this->api->fetchData());

        return Command::SUCCESS;
    }
}