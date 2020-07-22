# Pok&eacute;monFinderApp

### Get Pok&eacute;mon info with PHP and NodeJS

This repository contains a personal implementation of pokeapi.co (filtered for the api/v2/pokemon/ path only) with PHP and NodeJS purely for training purposes.

Enter the name of the pok&eacute;mon you want to search for and send the request for information.

It's possible to make the request:

-   locally (search and get data in api/v2 directory)
-   to the public Api (pokeapi.co)

You can choose to use:

-   PHP 7.1.3+
-   NodeJs 10+ (Express)

After every request the result will be cached about 2 minutes (local mode) or about 5 minutes (api mode).

**Stack/Keywords:** PHP, Javascript, Css, NodeJs, Nginx, Docker, Bash/Zsh, Api, Cache, Tests, Debug
**Tools/Packages:** Symfony Http, Symfony Cache, PHP cURL, Codeception, PHPStan, CodeSniffer, PHP-CS-Fixer, Xdebug, Express, ESlint, Prettier, Jest, Axios, NpmCacache, Docker-Compose

### TODO before

#### Enable requests to "Local" (radio button)

-   unzip api/v2.zip file (the unzipped path should be api/v2/pokemon/...)

#### Docker only - Enable and set env variables

-   rename docker/.env.example to docker/.env and edit it.
-   remove the commented docker line in ./codeception.yml

## Starting the environment

### RUN ON LOCALHOST

**Requirements:** php 7.1.3+, node 10+

#### Starting PHP server

**Directory**: ./

##### With Symfony CLI

```console
$ symfony local:server:start --port=8000 --no-tls -d
$ symfony local:server:logs
```

##### Or with built-in PHP server

```console
$ php -S localhost:8000 -t public
```

#### Starting NODE server

**Directory**: ./node

```console
$ PORT=5000 npm run dev
```

#### Final environments

-   SERVER
    `http://localhost:8000`
-   NODE
    `http://localhost:5000`

### RUN ON DOCKER (recommended)

**Requirements:** docker, docker-compose 3+

#### Starting DOCKER containers

**Directory**: ./docker

##### At first starting

```console
$ docker-compose up --build --abort-on-container-exit
```

##### With built images you can run only

```console
$ docker-compose up
```

#### Final environments

<small>_NOTICE: DOMAIN (myapp.wip) depend on .env ENV_HOST_DOMAIN value_</small>

-   SERVER PROD
    `http://myapp.wip` or `http://myapp.wip:8000`
-   SERVER DEV (with Xdebug)
    `http://myapp.wip:8001`
-   NODE (nginx proxy upstream to port 3000)
    `http://myapp.wip:5000`

#### Docker container SSH access - PHP DEV server example

<small>_NOTICE: USER (-u docker) depend on .env ENV_CONTAINER_USER value_</small>

<small>**User access**</small>

```console
$ docker-compose exec -u docker server_php_fpm_dev zsh
```

<small>**Root access**</small>

```console
$ docker-compose exec server_php_fpm_dev zsh
```
