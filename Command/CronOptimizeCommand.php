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
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;
use Symfony\Component\Console\Question\Question;
use Skrip42\Bundle\CronBundle\Services\Cron;
use DateTime;

class CronOptimizeCommand extends Command
{
    protected static $defaultName = 'cron:optimize';

    protected $container;

    public function __construct(Cron $scheduler, ContainerInterface $container)
    {
        $this->scheduler = $scheduler;
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this ->setDescription('add new cron row');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $count = $this->scheduler->optimize();
        if (empty($count)) {
            $io->success("nothing optimize");
        } else {
            $io->success("$count shedules deprecated and was by disabled");
        }
        return 0;
    }
}
