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

class CronUpdateCommand extends Command
{
    protected static $defaultName = 'cron:update';

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this ->setDescription('update cron task')
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
        $question = new Question(
            "Enter schedule pattern(empty to {$schedule->getPattern()}):",
            $schedule->getPattern()
        );
        $validator = function ($pattern) {
            return preg_match('/(?:[*\/()+\-\d,:!]+(?:_|\Z)){6}/', $pattern) === 1;
        };
        do {
            $pattern = $helper->ask($input, $output, $question);
        } while (!$validator($pattern));

        $question = new Question(
            "Enter schedule command(empty to {$schedule->getCommand()}):",
            $schedule->getCommand()
        );
        $question->setAutocompleterValues(array_keys($this->getApplication()->all()));
        $validator = function ($command) {
            return $this->getApplication()->has($command);
        };
        do {
            $command = $helper->ask($input, $output, $question);
        } while (!$validator($command));
        $schedule->setPattern($pattern);
        $schedule->setCommand($command);

        $manager = $this->container->get('doctrine')->getManager();
        $manager->flush();

        $io->success("schedyle is created!");

        return 0;
    }
}
