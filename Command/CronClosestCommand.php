<?php

namespace Skrip42\Bundle\CronBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Skrip42\Bundle\CronBundle\Services\Cron;
use DateTime;

class CronClosestCommand extends Command
{
    protected static $defaultName = 'cron:closest';

    protected $scheduler;

    protected $container;

    protected $perPage = 20;

    public function __construct(Cron $scheduler, ContainerInterface $container)
    {
        $this->scheduler = $scheduler;
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this ->setDescription('print list of cron task')
              ->addOption(
                  'count',
                  'c',
                  InputOption::VALUE_OPTIONAL,
                  'count',
                  15
              );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $input->getOption('count');

        $closest = $this->scheduler->closestList($count);
        $table = new Table($output);
        $table->setHeaders(['ID', 'Command', 'Date']);
        foreach ($closest as $row) {
            $table->addRow($row);
        }
        $table->render();
        return 0;
    }
}
