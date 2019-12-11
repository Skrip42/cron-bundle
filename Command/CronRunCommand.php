<?php

namespace Skrip42\Bundle\CronBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Skrip42\Bundle\CronBundle\Services\Cron;
use DateTime;

class CronRunCommand extends Command
{
    protected static $defaultName = 'cron:run';

    protected $scheduler;

    protected $container;

    public function __construct(Cron $scheduler, ContainerInterface $container)
    {
        $this->scheduler = $scheduler;
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this ->setDescription('running schedule command') ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $schedules = $this->scheduler->getActualOnCurrentTime();

        foreach ($schedules as $schedule) {
            $command = explode(' ', $schedule->getCommand());
            $ex = $this->getApplication()->find($command[0]);
            $arguments = [
                'command' => $command[0]
            ];
            for ($i = 1; $i < count($command); $i++) {
                if (strpos($command[$i], '-') === 0) {
                    $arguments[$command[$i]] = true;
                } else {
                    $arguments["arg" . $i] = $command[$i];
                }
            }
            $greetInput = new ArrayInput($arguments);
            $output->writeln('executing ' . $command[0] . ':');
            $returnCode = $ex->run($greetInput, $output);
            $schedule->incRunningCounter();
            $schedule->setLastRunning(new DateTime('now'));
        }
        $this->container->get('doctrine')->getManager()->flush($schedules);
        $io->success('all schedule command executed');
        return 0;
    }
}
