# cron-bundle

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

## available command:
- `cron:add` - interactiv adding chron task
- `cron:closest` - show list of closest task
- `cron:list` - show list of chron task
- `cron:optimize` - disable outdated chron tasks
- `cron:run` - run actual task

## chron syntax:
task pattern is `{minute}_{house}_{day}_{weekday}_{month}_{year}`


|`*` | run everytime | `*_*_*_*_*_* `- will be executed every minute |
|`n,m` | run in n and m | `5,10_*_*_*_*_*` - will be executed in 5 and 10 minute |
|`!n` | run everytime except n | `*_*_*_!1_*_*` - will be executed everytime except Monday |
|`n+m` | run in n+m | `5+10_*_*_*_*_*` - will be executed in 15 minutes |
|`n-m` | run in n-m | `15-10_*_*_*_*_*` - will be execute in 5 minutes |
|`-n` | run in m-n where m is max value of range (-5 in minute part equal 55 minute) | `*_*_- 1_*_*_*` - will be executed on the last day of the month |
|`n/m` | run when n aliquot m (*/5 in minute part - run every 5 minute) | `*/5_*_*_*_*_*` - will be executed every 5 minutes |
|`n:m` | run in range from n to m | `5:10_*_*_*_*_*` - will be executed in 5,6,7,8,9 and 10 minutes |
|`(n)` | group of operators | `!(5,10,15)_*_*_*_*_*` - will be executed every minute except 5,10 and 15 minute |

Operators can be combined, for example: `0_0_*/2-1_*_*_*` - will be executed at midnight on odd days.
