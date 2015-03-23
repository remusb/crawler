<?php
namespace Manager\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Manager\Manager;

class RunCommand extends Command {
    protected function configure() {
        $this
            ->setName('run')
            ->setDescription('Run the manager')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $count = Manager::Instance()->run();

        $output->writeln("Processed {$count} URL(s)");
    }
}