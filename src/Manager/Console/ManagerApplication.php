<?php
namespace Manager\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class ManagerApplication extends Application
{
    const NAME = 'Manager\' Console Application';
    const VERSION = '1.0';

    public function __construct() {
        parent::__construct(static::NAME, static::VERSION);

        // $this->add(new Command\FetchCommand);
        $this->add(new Command\RunCommand);
        $this->add(new Command\ClearCommand);
        $this->add(new Command\ConfigureCommand);
    }
}