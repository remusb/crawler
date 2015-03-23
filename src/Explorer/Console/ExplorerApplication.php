<?php
namespace Explorer\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class ExplorerApplication extends Application
{
    const NAME = 'Explorer\' Console Application';
    const VERSION = '1.0';

    public function __construct() {
        parent::__construct(static::NAME, static::VERSION);

        $this->add(new Command\SeedCommand);
        $this->add(new Command\InjectCommand);
        $this->add(new Command\FetchCommand);
    }
}