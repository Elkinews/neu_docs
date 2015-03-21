# Sail Commands

Sail is a wrapper around `docker-compose` which supports some Laravel-specific
commands to make development work quicker. Any command not explicitly listed
which works with `docker-compose` is also supported.

If you run Sail without any arguments, then `docker-compose ps` is executed
showing the list of containers.

See the [Laravel Sail documentation](https://laravel.com/docs/8.x/sail) for more
information.

## art / artisan

Proxy Artisan commands to the `artisan` binary on the application container.

Run `./sail artisan` to see a list of available commands. See the
[documentation](https://laravel.com/docs/8.x/sail#executing-artisan-commands) for
more information.

## bash / shell

Initiate a Bash shell within the application container. See the
[documentation](https://laravel.com/docs/8.x/sail#sail-container-cli) for more
information.

## bin

Proxy vendor binary commands on the application container.

Example:

```shell
# execute vendor/bin/phpunit
./sail bin phpunit
```

## composer

Proxy Composer commands to the `composer` binary on the application container.

Run `./sail composer` to see a list of available commands. See the
[documentation](https://laravel.com/docs/8.x/sail#executing-composer-commands) for
more information.

## debug

Proxy the "debug" command to the `php artisan` binary on the application
container with xdebug enabled. See the
[documentation](https://laravel.com/docs/8.x/sail#xdebug-cli-usage) for more
information.

## dusk

Proxy the "dusk" command to the `php artisan dusk` Artisan command.

Run `./sail dusk -h` for help. See the
[documentation](https://laravel.com/docs/8.x/sail#laravel-dusk) for more
information.

## dusk:fails

Proxy the "dusk:fails" command to the `php artisan dusk:fails` Artisan command.

Run `./sail dusk:fails -h` for help.

## node

Proxy Node commands to the `node` binary on the application container.

Run `./sail node -h` for help. See the
[documentation](https://laravel.com/docs/8.x/sail#executing-node-npm-commands) for
more information.

## npm

Proxy NPM commands to the `npm` binary on the application container.

Run `./sail npm -h` for help. See the
[documentation](https://laravel.com/docs/8.x/sail#executing-node-npm-commands) for
more information.

## npx

Proxy NPX commands to the `npx` binary on the application container.

Run `./sail npx -h` for help. See the
[documentation](https://laravel.com/docs/8.x/sail#executing-node-npm-commands) for
more information.

## php

Proxy PHP commands to the `php` binary on the application container.

Run `./sail php -h` for help. See the
[documentation](https://laravel.com/docs/8.x/sail#executing-php-commands) for more
information.

## root-shell

Initiate a root user Bash shell within the application container. See the
[documentation](https://laravel.com/docs/8.x/sail#sail-container-cli) for more
information.

## share

Share the site. See the
[documentation](https://laravel.com/docs/8.x/sail#sharing-your-site) for more
information.

## test

Proxy the "test" command to the `php artisan test` Artisan command.

Run `./sail test -h` for help. See the
[documentation](https://laravel.com/docs/8.x/sail#running-tests) for more
information.

## tinker

Initiate a Laravel Tinker session within the application container. See the
[documentation](https://laravel.com/docs/8.x/sail#sail-container-cli) for more
information.
