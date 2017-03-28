<?php

// src/AppBundle/Command/CreateUserCommand.php
namespace Frian\UtilsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputOption;

class RecreateDoctrineDatabaseCommand extends Command
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('utils:doctrine:recreate')

            ->addOption('force', null, InputOption::VALUE_NONE, 'Needed for compatibility with doctrine:database:drop')

            ->addOption('no-drop', null, InputOption::VALUE_NONE, 'Needed when the database does not exist')

            // the short description shown while running "php bin/console list"
            ->setDescription('Recreate the database and load fixtures')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(<<<'EOF'

The <info>%command.name%</info> drops and creates the database and load fixtures:

  <info>php %command.full_name% </info>

For compatibility reasons you have to specifiy the <comment>--force</comment> option:

  <info>php %command.full_name% --force</info>

If you have no database add the <comment>--no-drop</comment> option

  <info>php %command.full_name% --force --no-drop</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln(['', 'Recreating database', '']);

        /**
         * Drop database
         */
        $command = $this->getApplication()->find('doctrine:database:drop');


        if ($input->getOption('force')) {

            // set --force
            $arguments = array(
                'command' => 'doctrine:database:drop',
                '--force'  => true,
            );
        }
        else {
            $arguments = array();
        }


        $returnCode = $this->executeCommand('doctrine:database:drop', $arguments, $output);

        $this->dropErrorHandler($returnCode, $input, $output);


        /**
         * Create database
         */
        $this->executeCommand('doctrine:database:create', [], $output);

        $this->errorHandler($returnCode, $output);


        /**
         * Create schema
         */
        $this->executeCommand('doctrine:schema:create', [], $output);

        $this->errorHandler($returnCode, $output);


        $output->writeln(['', 'done', '']);

    }

    private function executeCommand(string $name, array $parameters, OutputInterface $output)
    {
        return $this->getApplication()
            ->find($name)
            ->run(new ArrayInput($parameters), $output);
    }

    private function errorHandler(int $returnCode, OutputInterface $output)
    {
        if ($returnCode) {
            $output->writeln(['', 'aborted', '']);
            die;
        }
    }

    private function dropErrorHandler(int $returnCode, InputInterface $input, OutputInterface $output)
    {
        if ($returnCode) {
            if ($input->getOption('no-drop')) {
                return;
            }
            $output->writeln(['', 'aborted', '']);
            $output->writeln(['you can add --no-drop', '']);
            die;
        }
    }

}
