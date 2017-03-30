<?php

// src/AppBundle/Command/CreateUserCommand.php
namespace Frian\ConsoleUtilsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecreateDoctrineDatabaseCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('utils:doctrine:recreate')

            // option --force for doctrine:database:drop
            ->addOption('force', '-f', InputOption::VALUE_NONE, 'Needed for compatibility with doctrine:database:drop')

            // option --fixtures for doctrine:fixtures:load
            ->addOption('fixtures', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The directory to load data fixtures from.')

            // option --em for doctrine:fixtures:load
            ->addOption('em', null, InputOption::VALUE_REQUIRED, 'The entity manager to use for this command.')

            // the short description shown while running "php bin/console list"
            ->setDescription('Recreates the database and loads fixtures')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> recreates the database and loads fixtures:

  <info>php %command.full_name% </info>

For compatibility reasons you have to specifiy the <comment>--force</comment> option:

  <info>php %command.full_name% --force</info>

You can use the <comment>--fixtures</comment> and <comment>--em</comment> option from <info>doctrine:fixtures:load</info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['', 'Recreating database', '']);

        /*
         * Drop database
         */
        // set default options
        $arguments = ['--if-exists' => true];

        // pass --force to doctrine:database:drop
        if ($input->getOption('force')) {
            $arguments['--force'] = true;
        }

        // exec
        $returnCode = $this->executeCommand('doctrine:database:drop', $arguments, $output);

        // handle error
        $this->errorHandler($returnCode, $output);


        /*
         * Create database
         */
        $returnCode = $this->executeCommand('doctrine:database:create', [], $output);

        $this->errorHandler($returnCode, $output);


        /*
         * Create schema
         */
        $returnCode = $this->executeCommand('doctrine:schema:create', [], $output);

        $this->errorHandler($returnCode, $output);


        /*
         * Load fixtures
         */
        // set default options
        $arguments = [];

        // pass --fixtures to doctrine:fixtures:load
        if ($input->getOption('fixtures')) {
            $arguments['--fixtures'] = $input->getOption('fixtures');
        }

        // pass --em to doctrine:fixtures:load
        if ($input->getOption('em')) {
            $arguments['--em'] = $input->getOption('em');
        }

        // exec
        $returnCode = $this->executeCommand('doctrine:fixtures:load', $arguments, $output, true);

        // handle error
        $this->errorHandler($returnCode, $output);


        $output->writeln(['', 'done', '']);
    }

    /**
     * Execute a command
     *
     * @return  int     returnCode
     */
     private function executeCommand(string $name, array $parameters, OutputInterface $output, bool $noInteraction = null  )
     {
         $input = new ArrayInput($parameters);

         // set input to no interaction
         if ($noInteraction) {
             $input->setInteractive(false);
         }

         return $this->getApplication()
             ->find($name)
             ->run($input, $output);
     }

    /**
     * Default error handler
     *
     * Exit on error
     */
    private function errorHandler(int $returnCode, OutputInterface $output)
    {
        if ($returnCode) {
            $output->writeln(['', 'aborted', '']);
            exit;
        }
    }
}
