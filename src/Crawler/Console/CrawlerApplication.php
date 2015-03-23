<?php
namespace Crawler\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class CrawlerApplication extends Application
{
    const NAME = 'Crawler\' Console Application';
    const VERSION = '1.0';

    public function __construct() {
        parent::__construct(static::NAME, static::VERSION);

        $this->add(new Command\RunCommand);
    }
}