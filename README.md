# chron-bundle

## install:
- add `    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/Skrip42/chron-bundle"
        }
    ],
` in you composer.json
- run `composer require skrip42/chron-bundle @dev
- create database `php ./bin/console make:migration` and `php ./bin/console doctrine:migration:migrate`
- add `*       *       *       *       *       ./bin/console chron:run` in you crontab 

## available command:
- `chron:add` - interactiv adding chron task
- `chron:closest` - show list of closest task
- `chron:list` - show chron task
- `chron:optimize` - disable depricated chron task
- `chron:run` - run actual task

## chron syntax:
task pattern is `{minute}_{house}_{day}_{weekday}_{month}_{year}`

`*` - run everytime
`n,m` - run in n and m
`!n` - run everytime except n
`n+m` - run in n+m
`n-m` - run in n-m
`-n` - run in m-n where m is max value of range (-5 in minute part equal 55 minute)
`n/m` - run when n aliquot m (*/5 in minute part - run every 5 minute)
`n:m` - run in range n - m
(n) - group 
