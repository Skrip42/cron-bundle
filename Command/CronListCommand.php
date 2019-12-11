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
use Skrip42\Bundle\CronBundle\Entity\Schedule;
use DateTime;

class CronListCommand extends Command
{
    protected static $defaultName = 'cron:list';

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
                  'all',
                  null,
                  InputOption::VALUE_OPTIONAL,
                  'show disabled?',
                  false
              )
              ->addOption(
                  'page',
                  'p',
                  InputOption::VALUE_OPTIONAL,
                  'page',
                  1
              );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $allView = $input->getOption('all');
        if ($allView !== false) {
            $allView = true;
        }
        $page = $input->getOption('page');
        if (!$page) {
            $page = 1;
        }
        $repository = $this->container->get('doctrine')
            ->getRepository(Schedule::class);
        $schedules = $repository->getPart($this->perPage, $this->perPage * ($page - 1), $allView);
        $scheduleCount = $repository->getCount();
        $pages = ceil($scheduleCount / $this->perPage);
        $table = new Table($output);
        $table->setHeaders(['ID', 'Command', 'Pattern', 'Last running', 'Running counter', 'Active']);
        foreach ($schedules as $schedule) {
            $table->addRow([
                $schedule->getId(),
                $schedule->getCommand(),
                $schedule->getPattern(),
                $schedule->getLastRunning(),
                $schedule->getRunningCounter(),
                $schedule->getActive()
            ]);
        }
        if ($scheduleCount > $this->perPage) {
            $table->setFooterTitle("Page $page/$pages");
        }
        $table->render();
        return 0;
    }
}
