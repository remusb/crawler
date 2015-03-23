<?php
namespace Manager\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Manager\Manager;

class ClearCommand extends Command {
    protected function configure() {
        $this
            ->setName('clear')
            ->setDescription('Clear queues')
            ->addArgument(
                'table',
                InputArgument::REQUIRED,
                'What do you want to clean? [queue|queue_pending]'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $action = $input->getArgument('table');
        $t = null;

        switch ($action) {
            case 'queue':
                $t = Manager::Instance()->clearQueue();
                break;
            case 'queue_pending':
                $t = Manager::Instance()->clearQueuePending();
                break;
        }

        var_dump($t);
    }
}