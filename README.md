# Status Board Backend

## Fetch fresh data

Execute this command manually or in a cron set for every minute

```shell
bin/console app:sync-datas
```

## Expose API

Run this symfony app through a php webserver (Symfony dev server, apache or nginx/php-fpm)

```shell
symfony serve
```

Access the modules datas on `http://localhost:8000/api/:module` where `:module` is a module id.

### Available modules

- netatmo

## Create a module