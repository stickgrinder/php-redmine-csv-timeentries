<?php

use src\Commands\LoadCommand;
use Symfony\Component\Console\Application;

// Include commands and dependencies...
require_once 'vendor/autoload.php';

$application = new Application();
$application->add(new LoadCommand());
$application->run();