<?php
namespace Agavee\TimeEntries\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Keboola\Csv;
use Redmine;

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
                'verbose',
                InputOption::VALUE_NONE,
                'Gives complete output for each entry'
            )
            ->addOption(
                'dry-run',
                InputOption::VALUE_NONE,
                'Simulates the loading using console output but does not load content to Redmine'
            )
            ->addOption(
                'force',
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

            // reading CSV file specified in parameters
            $csv = new CsvFile($input->getArgument('input'));

            // Do the actual job
            try {
                // instantiating API client
                $client = new Client($config['redmine_url'],$config['consumer_key']);

                // foreach row in CSV file, load it and show proper output
                foreach($csv as $row) {
                    if (!$input->getOption('dry-run')) {
                        //$client->api('time_entry')->create($row);
                        var_dump($row);
                    }
                    if ($input->getOption('dry-run') || $input->getOption('verbose')) {
                        $ouput->writeln('<info>Time entry added</info>');
                    }
                }

                $output->writeln('<comment>'.count($csv).' loaded</comment>');

            } catch(\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
        }
    }
}