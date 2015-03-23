<?php
namespace Explorer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Explorer\Explorer;

class InjectCommand extends Command {
    protected function configure() {
        $this
            ->setName('inject')
            ->setDescription('Add url')
            ->addArgument(
                'url',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'List of url'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $urlArray = $input->getArgument('url');

        Explorer::Instance()->addUrlList($urlArray);

        $output->writeln('Ok');
    }
}