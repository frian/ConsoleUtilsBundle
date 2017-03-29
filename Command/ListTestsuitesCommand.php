<?php

// src/AppBundle/Command/ListTestsuitesCommand.php
namespace Frian\ConsoleUtilsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\DomCrawler\Crawler;

class ListTestsuitesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('utils:testsuite:list')

            // the short description shown while running "php bin/console list"
            ->setDescription('List testsuites')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> lists testsuites defined in phpunit.xml:

  <info>php %command.full_name% </info>
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get root dir (app)
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();

        $file = 'phpunit.xml';

        // get path to phpunit.xml
        $phpunitPath = $rootDir.'/../';

        $phpunitPath = preg_replace('/([^\/.]+\/(?1)?\.\.\/)/', '', $phpunitPath);

        // get path to phpunit.xml for sf2
        $phpunitPathSf2 = $rootDir.'/';

        // phpunit.xml file path
        $phpunitFile = $phpunitPath.$file;

        // check if file exists
        if (!file_exists($phpunitFile)) {

            // phpunit.xml file path for sf2
            $phpunitFile = $phpunitPathSf2.$file;

            if (!file_exists($phpunitPathSf2.$file)) {
                $output->writeln(['', "phpunit.xml not found in $phpunitPath and $phpunitPathSf2.", '', 'Aborted', '']);
                exit;
            }
        }

        // loaf file
        $crawler = new Crawler(file_get_contents($phpunitFile));

        // get testsuites
        $items = $crawler->filterXPath('//testsuite');

        // output
        if ($items->count() > 0) {

            $output->writeln('<comment>Available testsuites:</comment>');

            for ($i=0; $i < $items->count(); $i++) {
                $output->writeln(' <info>'.$items->eq($i)->attr('name')).'</info>';
            }
        }
        else {
            $output->writeln('No testsuites available');
        }
    }
}
