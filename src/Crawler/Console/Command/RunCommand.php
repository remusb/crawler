<?php
namespace Crawler\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Crawler\Crawler;

class RunCommand extends Command {
    protected function configure() {
        $this
            ->setName('run')
            ->setDescription('Run the crawler')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        Crawler::Instance()->run($output);
    }
}