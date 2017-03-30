<?php

// src/AppBundle/Command/ListTestsuitesCommand.php
namespace Frian\ConsoleUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\DomCrawler\Crawler;

class RunTestsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('utils:doctrine:test')

            // option --force for doctrine:database:drop
            ->addOption('force', '-f', InputOption::VALUE_NONE, 'Needed for compatibility with doctrine:database:drop')

            // option --fixtures for doctrine:fixtures:load
            ->addOption('fixtures', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The directory to load data fixtures from.')

            // option --testsuite for phpunit
            ->addOption('testsuite', '-t', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Filter which testsuite to run.')

            // the short description shown while running "php bin/console list"
            ->setDescription('Runs phpunit tests on a recreated database')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> executes phpunit tests:

  <info>php %command.full_name% </info>

For compatibility reasons you have to specifiy the <comment>--force</comment> option:

  <info>php %command.full_name% --force</info>

You can use the <comment>--fixtures</comment> option from <info>doctrine:fixtures:load</info>

You can use the <comment>--testsuite</comment> option from <info>phpunit</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // set default options
        $arguments = [];

        // pass --force to utils:doctrine:recreate
        if ($input->getOption('force')) {
            $arguments['--force'] = $input->getOption('force');
        }

        // pass --fixtures to utils:doctrine:recreate
        if ($input->getOption('fixtures')) {
            $arguments['--fixtures'] = $input->getOption('fixtures');
        }

        // $this->getApplication()
        //     ->find('utils:doctrine:recreate')
        //     ->run(new ArrayInput($arguments), $output);



        // get root dir (app)
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();

        // phpunit.xml
        $phpunitFile = 'phpunit.xml';


        // path to phpunit.xml
        $phpunitPathSf3 = $rootDir.'/../';

        // path without /../
        $phpunitPathSf3 = preg_replace('/([^\/.]+\/(?1)?\.\.\/)/', '', $phpunitPathSf3);


        // path to phpunit.xml for sf2
        $phpunitPathSf2 = $rootDir.'/';


        // phpunit.xml path
        $phpunitPath = $phpunitPathSf3;

        // phpunit.xml file path
        $phpunitFilePath = $phpunitPath.$phpunitFile;


        // check if file exists
        if (!file_exists($phpunitFilePath)) {

            // phpunit.xml file path for sf2
            $phpunitFilePath = $phpunitPathSf2.$phpunitFile;

            $phpunitPath = $phpunitPathSf2;

            if (!file_exists($phpunitFilePath)) {
                $output->writeln(['', "<error>  $phpunitFile not found in $phpunitPathSf3 and $phpunitPathSf2.</error>", '', 'Aborted', '']);
                exit;
            }
        }


        // create command
        $command = 'phpunit';

        // add -c flag if needed
        if (preg_match("/app/", $phpunitPath)) {
            $command .= ' -c app';
        }

        // add testsuite option
        if ($input->getOption('testsuite')) {
            $command .= ' --testsuite ' . $input->getOption('testsuite')[0];
        }


        // run
        system($command);
    }
}
