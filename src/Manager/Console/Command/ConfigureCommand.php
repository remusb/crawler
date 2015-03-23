<?php
namespace Manager\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Manager\Manager;

class ConfigureCommand extends Command {
    protected function configure() {
        $this
            ->setName('configure')
            ->setDescription('Configure system')
            ->addOption(
               'remove',
                null,
                InputOption::VALUE_NONE,
               'Remove the entry?'
            )
            ->addArgument(
                'system',
                InputArgument::REQUIRED,
                'What do you want to configure? [blacklist]'
            )
            ->addArgument(
                'entry',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Specify entries. Separate by space'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $action = $input->getArgument('system');

        switch ($action) {
            case 'blacklist':
                $this->configureBlacklist($input->getArgument('entry'), $input->getOption('remove'), $output);
                break;
        }
    }

    public function configureBlacklist($entries, $remove, OutputInterface $output) {
        $status = '';

        if ($remove) {
            $status = Manager::Instance()->removeBlacklist($entries);
        } else {
            $status = Manager::Instance()->addBlacklist($entries);
        }

        $output->writeln($status);
    }
}