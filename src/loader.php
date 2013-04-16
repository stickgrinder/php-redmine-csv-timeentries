<?php

// Include dependencies...
require_once '../vendor/autoload.php';
// And commands
require_once 'commands/load.php';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new LoadCommand);
$application->run();