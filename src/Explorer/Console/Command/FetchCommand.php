<?php
namespace Explorer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Explorer\Explorer;

class FetchCommand extends Command {
    protected function configure() {
        $this
            ->setName('fetch')
            ->setDescription('Get the list of url')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $urlList = Explorer::Instance()->fetchUrlList();

        var_dump($urlList);
    }
}