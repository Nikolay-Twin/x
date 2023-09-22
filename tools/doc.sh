#!/usr/bin/.env bash

doc() {
   printf "

   ------ Управление ./tools/app.sh <options> [<options>] ------

options:
   init                          - инициализация нового проекта
   build                         - построить контейнер
   up                            - запустить контейнер
   down                          - остановить контейнер
   migration <options>           - миграции (generate,
                                             up,
                                             execute,
                                             down,
                                             status)
   composer <options>            - композер
   php <options>                 - PHP команды в контейнере
   exec <options>                - произвольные команды в контейнере


   "
}