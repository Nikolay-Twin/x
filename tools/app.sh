#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
source "${DIR}/doc.sh"
source "${DIR}/common.sh"

checkDockerCompose() {
  if ! [ -f ${DIR}/../docker-compose.yml ]; then
    log "docker-compose.yml not exist." "error"
      printf "\e[0m\n"
    exit
  fi
}

createEnv() {
  if ! [ -f ${DIR}/.env ]; then
     cp ${DIR}/.env.dist ${DIR}/../.env
  fi
  export $(grep -v '#' "$DIR/../.env" | xargs)
}

checkEnv() {
  if ! [ -f ${DIR}/../.env ]; then
    log "File .env not exist" "error"
      printf "\e[0m\n"
    exit
  fi
}

init() {
  createEnv
  dockerBuild
  inastall
  printf "\e[0m\n"
  log "Project initialization completed. Enter the domain x.not-real.ru in hosts" "wait"
  printf "\e[0m\n"
}

dockerBuild() {
  checkDockerCompose
  checkEnv
  log "Docker containers initializing." "wait"
  docker network create xweb
  docker-compose build
  docker-compose up -d
  printf "\e[0m\n"
  log "NEXT" "info"
  printf "\e[0m\n"
}

inastall() {
  chmod 777 "${DIR}/../src/"
  log "Composer install. Please, wait..." "wait"
  composer install  --no-interaction --no-progress --prefer-dist
  chmod 775 "${DIR}/../src/"
  if ! [ -f "${DIR}/../src/composer.lock" ]; then
    printf "\e[0m\n"
    log "Composer installation problem. Installation aborted" "error"
    printf "\e[0m\n"
    exit
  fi
  log "NEXT" "info"
  chmod 777 "${DIR}/../src/data"
  chmod 777 "${DIR}/../src/data/cache"
  migration up
}

up() {
  down
  docker-compose up -d
}

down() {
  checkDockerCompose
  docker-compose down
}

build() {
  checkDockerCompose
  docker-compose build
}

php() {
  docker-compose exec php ${@:1}
}

composer() {
  exec composer ${@:1}
}

exec() {
  createEnv
  docker exec -it "${CONTAINER_PREFIX}_${CONTAINER_NAME}_php" ${@:1}
}

migration() {
  log "Applying migrations" "wait"
  exec vendor/laminas/laminas-cli/bin/laminas migration:${@:1}
}

case "$1" in
  "init")
    init;;
  "build")
    build;;
  "up")
    up;;
  "down")
    down;;
  "migration"*)
    migration ${@:2};;
  "php"*)
    php ${@:2};;
  "composer"*)
    composer ${@:2};;
  "exec"*)
    exec ${$:2};;
  *)
    doc;;
esac