# cron-bundle
task scheduler for symfony witch extended syntax similar to cron.

## install:
- add `    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Skrip42/cron-bundle"
        }
    ],
` in you composer.json
- run `composer require skrip42/cron-bundle @dev
- create database `php ./bin/console make:migration` and `php ./bin/console doctrine:migration:migrate`
- add `*       *       *       *       *       ./bin/console cron:run` in you crontab 

## schedule-task syntax:
task pattern is `{minute}_{house}_{day}_{weekday}_{month}_{year}`

| operant | description | example |
|:---:|:----|:---|
| `*` | run everytime | `*_*_*_*_*_* `- will be executed every minute |
| `n,m` | run in n and m | `5,10_*_*_*_*_*` - will be executed in 5 and 10 minute |
| `!n` | run everytime except n | `*_*_*_!1_*_*` - will be executed everytime except Monday |
| `n+m` | run in n+m | `5+10_*_*_*_*_*` - will be executed in 15 minutes |
| `n-m` | run in n-m | `15-10_*_*_*_*_*` - will be execute in 5 minutes |
| `-n` | run in m-n where m is max value of range (-5 in minute part equal 55 minute) | `*_*_- 1_*_*_*` - will be executed on the last day of the month |
| `n/m` | run when n aliquot m (*/5 in minute part - run every 5 minute) | `*/5_*_*_*_*_*` - will be executed every 5 minutes |
| `n:m` | run in range from n to m | `5:10_*_*_*_*_*` - will be executed in 5,6,7,8,9 and 10 minutes |
| `(n)` | group of operators | `!(5,10,15)_*_*_*_*_*` - will be executed every minute except 5,10 and 15 minute |

Operators can be combined, for example: `0_0_*/2-1_*_*_*` - will be executed at midnight on odd days.


## available command:
- `cron:add` - interactiv adding cron task
- `cron:closest` - show list of closest task
- `cron:list` - show list of cron task
- `cron:list --all` - show list of all activity status cron task
- `cron:optimize` - disable outdated cron tasks
- `cron:run` - run actual task
- `cron:update $id` - update cron task
- `cron:toggle $id` - toggle cron task activity

## usage:
```php
....... //you namespace declaration

use Skrip42\Bundle\CronBundle\Services\Cron;
use Skrip42\Bundle\CronBundle\Component\Pattern;

....... //you class declaration

protected $cron; //instance of Cron service

public function __construct(Cron $cron) {
    $this->cron = $cron
}

........ //in you method
$this->cron->getActualOnCurrentTime(); // return array of actual Schedule entity
$this->cron->optimize(); // disable outdated cron tasks
$this->cron->closestList($count); // return $count closest task in format: [$id, $command, $c]
$this->cron->getList($all); // return all schedule task? if $all = true includes disabled task
$this->cron->addSchedule($patternString, $commandString); // create new schedule task
$this->cron->toggleSchedule($id); // toggle schedule task activity
$this->cron->updateSchedule($id, $patternString, $commandString); // update schedule task

$pattern = new Pattern($patternString); // get pattern object
$pattern->test(new DateTime('NOW')); // test pattern for match date
$pattern->getClosest($count); //return $count closest date that match pattern

$schedule = reset($this->cron->getList()); // schedule is standart doctrine entity
$schedule->getId(); //return id
$schedule->getCommand(); // return command string
$schedule->setCommand($commandString); // set command string to schedule
$schedule->getPattern(); // return pattern string
$schedule->setPattern($patternString); // set pattern string to schedule
$schedule->toggleActive(); // toggle schedule activity

$this->container->get('doctrine')->getManager()->flush(); // to save change
........
```
