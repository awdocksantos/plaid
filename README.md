## Docker

* Install [Docker](https://docs.docker.com/install/)

[How to install Docker on Ubuntu](https://docs.docker.com/install/linux/docker-ce/ubuntu/):

create the docker group
```bash
sudo groupadd docker
sudo usermod -aG docker $USER
```

## Run project with Docker on localhost

run docker compose
```bash
docker-compose up -d --build nginx php code postgress adminer redis exim
```

install composer libraries
```bash
docker-compose exec --user $(id -u):$(id -g) php composer install
```

create database
```bash
docker-compose exec --user $(id -u):$(id -g) php bin/console doctrine:database:create
```

run database migrations
```bash
docker-compose exec --user $(id -u):$(id -g) php bin/console doctrine:migrations:migrate
```

load fixtures, dummy data
```bash
docker-compose exec --user $(id -u):$(id -g) php bin/console doctrine:fixtures:load
```

## Run project with Docker on production host.

build new prod images
```
docker-compose -f docker-compose.yml -f docker-compose.prod.yml build --force-rm --no-cache php code exim
```

push new images to repo
```
docker tag symfony-skeleton-code:latest denisnabatov/symfony-skeleton-code:latest
docker push denisnabatov/symfony-skeleton-code:latest

docker tag symfony-skeleton-code:latest denisnabatov/symfony-skeleton-code:$VERSION
docker push denisnabatov/symfony-skeleton-code:$VERSION

docker tag symfony-skeleton-php:latest denisnabatov/symfony-skeleton-php:latest
docker push denisnabatov/symfony-skeleton-php:latest

docker tag symfony-skeleton-exim:latest denisnabatov/symfony-skeleton-exim:latest
docker push denisnabatov/symfony-skeleton-exim:latest
```

run swarm on remote host
```
docker swarm init --advertise-addr $IP_ADVERTISE_ADDR
```

copy docker-stack.yml file to remote host and run docket stack deploy
```
docker stack deploy -c docker-stack.yml --with-registry-auth symfony-skeleton
```

update service
```
docker service update --replicase 2 symfony-skeleton-php
```
 