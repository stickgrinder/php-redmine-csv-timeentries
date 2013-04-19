<?php
namespace src\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Keboola\Csv\CsvFile;
use Redmine\Client;

class LoadCommand extends Command {

    protected function configure() {
        $this
            ->setName('load')
            ->setDescription('Load given CSV file to your Redmine instance')
            ->addArgument(
                'input',
                InputArgument::REQUIRED,
                'Which file do you want to load? (with path)'
            )
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'Simulates the loading using console output but does not load content to Redmine'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Load file contents even if the file has already been loaded *somewhen* in the past'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $config = parse_ini_file('config.ini', false);
        // a smoke-check of ini file (just to understand if the user compiled it)
        if (!$config || empty($config['redmine_url']) || empty($config['consumer_key'])) {
            $output->writeln('<error>You should provide a valid config.ini file to run this command</error>');
            $output->writeln('<comment>Look config.ini.dist for info</comment>');
        } else {

            $fhash = array(
                'name' => basename($input->getArgument('input')),
                'hash' => sha1_file($input->getArgument('input'))
            );
            $history = new CsvFile('history/history.csv');
            // check for file name to be loaded already
            if (!$input->getOption('force')) {
                foreach ($history as $item) {
                    if ($item[0] === $fhash['name']) {
                        if ($item[1] === $fhash['hash'])
                            $output->writeln('<error>It seems this file has already been loaded. Use --force if you really want to load this file.</error>');
                        else
                            $output->writeln('<error>It seems a file named '.$fhash['name'].' has already been loaded, but the content is not the same. If you are sure of what you are doing, run this command again with --force.</error>');
                        die();
                    }
                }
            }

            // reading CSV file specified in parameters
            $csv = new CsvFile($input->getArgument('input'));
            $header = $csv->getHeader();
            $first = true;

            // Do the actual job
            try {
                // instantiating API client
                $client = new Client($config['redmine_url'],$config['consumer_key']);

                // foreach row in CSV file, load it and show proper output
                foreach($csv as $row) {
                    if ($first) {
                        $first = false;
                        continue;
                    }
                    $entry = array_combine($header,$row);
                    if (!$input->getOption('dry-run')) {
                        $client->api('time_entry')->create($entry);
                    }
                    if ($input->getOption('dry-run') || $input->getOption('verbose')) {
                        $output->writeln(sprintf('<info>Time entry added: %s</info>',json_encode($entry)));
                    }
                }

                $output->writeln('<comment>'.(iterator_count($csv)-1).' loaded</comment>');
                if (!$input->getOption('dry-run'))
                    $history->writeRow($fhash); // TODO: this shit does not work! :(

            } catch(\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
        }
    }
}