<?php

// src/AppBundle/Command/CreateUserCommand.php
namespace Frian\ConsoleUtilsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputOption;

class RecreateDoctrineDatabaseCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('utils:doctrine:recreate')

            ->addOption('force', '-f', InputOption::VALUE_NONE, 'Needed for compatibility with doctrine:database:drop')

            ->addOption('fixtures', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The directory to load data fixtures from.')

            ->addOption('no-drop', null, InputOption::VALUE_NONE, 'Needed when the database does not exist')

            // the short description shown while running "php bin/console list"
            ->setDescription('Recreate the database and load fixtures')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(<<<'EOF'

The <info>%command.name%</info> recreates the database and loads fixtures:

  <info>php %command.full_name% </info>

For compatibility reasons you have to specifiy the <comment>--force</comment> option:

  <info>php %command.full_name% --force</info>

If you have no database add the <comment>--no-drop</comment> option

  <info>php %command.full_name% --force --no-drop</info>

You can use the <comment>--fixtures</comment> option from <info>doctrine:fixtures:load</info>

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
        // pass --force to doctrine:database:drop
        if ($input->getOption('force')) {

            $arguments = array(
                'command' => 'doctrine:database:drop',
                '--force'  => true,
            );
        }
        else {
            $arguments = array();
        }

        // ignore symfony error with --no-drop
        $commandOutput = $output;
        if ($input->getOption('no-drop')) {
            $commandOutput = new NullOutput();
        }

        // exec
        $returnCode = $this->executeCommand('doctrine:database:drop', $arguments, $commandOutput);

        // handle error
        $this->dropErrorHandler($returnCode, $input, $output);


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
        // pass --fixtures to doctrine:fixtures:load
        if ($input->getOption('fixtures')) {

            $arguments = array(
                'command' => 'doctrine:fixtures:load',
                '--fixtures'  => $input->getOption('fixtures'),
             );
        }
        else {
            $arguments = array();
        }

        // exec
        $returnCode = $this->executeCommand('doctrine:fixtures:load', $arguments, $output);

        // handle error
        $this->dropErrorHandler($returnCode, $input, $output);

        $output->writeln(['', 'done', '']);
    }

    /**
     * Execute a command
     *
     * @return  int     returnCode
     */
    private function executeCommand(string $name, array $parameters, OutputInterface $output)
    {
        return $this->getApplication()
            ->find($name)
            ->run(new ArrayInput($parameters), $output);
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

    /**
     * Database drop error handler
     *
     * Exit and show hint if --force
     * Return if --force and --no-drop
     * Exit
     */
    private function dropErrorHandler(int $returnCode, InputInterface $input, OutputInterface $output)
    {
        if ($returnCode) {

            if ($input->getOption('force') && ! $input->getOption('no-drop')) {
                $output->writeln(['', 'aborted', '']);
                $output->writeln(['you can add --no-drop', '']);
                exit;
            }
            elseif ($input->getOption('no-drop')) {
                return;
            }
            else {
                $output->writeln(['', 'aborted', '']);
                exit;
            }
        }
    }
}
