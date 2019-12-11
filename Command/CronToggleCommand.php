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
use Skrip42\Bundle\CronBundle\Entity\Schedule;
use DateTime;

class CronToggleCommand extends Command
{
    protected static $defaultName = 'cron:toggle';

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this ->setDescription('toggle cron task activity')
            ->addArgument('arg1', InputArgument::REQUIRED, 'ID of schedule');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('arg1');
        $helper = $this->getHelper('question');
        $repository = $this->container->get('doctrine')
                                      ->getRepository(Schedule::class);

        $schedule = $repository->find($id);
        $schedule->toggleActive();

        $manager = $this->container->get('doctrine')->getManager();
        $manager->flush();

        $io->success("schedyle is created!");

        return 0;
    }
}
