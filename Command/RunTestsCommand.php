<?php

// src/AppBundle/Command/ListTestsuitesCommand.php
namespace Frian\ConsoleUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->setName('utils:tests:run')

            // the short description shown while running "php bin/console list"
            ->setDescription('Runs phpunit tests')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> executes phpunit tests:

  <info>php %command.full_name% </info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        system('phpunit');
    }
}
