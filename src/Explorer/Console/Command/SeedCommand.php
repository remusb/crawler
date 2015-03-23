<?php
namespace Explorer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Explorer\Explorer;

class SeedCommand extends Command {
    protected function configure() {
        $this
            ->setName('seed')
            ->setDescription('Manage seed')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'What do you want to do? [print|add]'
            )
            // ->addOption(
            //    'urls',
            //    null,
            //     InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            //    'Add each url separated by a space'
            // )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $action = $input->getArgument('action');

        switch ($action) {
            case 'add':
                $this->add($output);
                break;
            case 'print':
                $this->printInfo($output);
                break;
        }
    }

    protected function add($output) {
        Explorer::Instance()->readInitialSeed();

        $output->writeln('Ok');
    }

    protected function printInfo($output) {
        var_dump(Explorer::Instance()->getInitialSeed());
    }
}