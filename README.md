# NDE Frontend

The new standalone frontend for NDE.

## Tech Specification

- PHP 7.4
- Laravel 8
- Vue 2

## Development

### Requirements

- Linux, macOS, Windows 10 version 2004 or higher, or Windows 11
- Windows Only, optional, recommended:
  [Windows Terminal](https://docs.microsoft.com/en-us/windows/terminal/install)
- Windows Only:
  [Windows Subsystem for Linux 2 (WSL2)](https://docs.microsoft.com/en-us/windows/wsl/install)
- Docker or [Docker Desktop](https://www.docker.com/products/docker-desktop)

### Getting Started

Open a terminal window/tab. If on Windows, make sure it's the shell of your WSL2
distribution.

In your terminal, run the following commands:

1. `./sail up -d` from the project's root directory to use
   [Laravel Sail](https://laravel.com/docs/8.x/sail).

   The first time you use this script, it will automatically detect if Sail is
   installed, and if not then it will run `composer install` and `npm install`
   in a temporary Docker container.

   Also, the first time you run the `up` command it will take a few minutes, but
   after that it should be fairly quick.
2. `./sail bash -c 'cp .env.example .env'`
3. `./sail art key:generate --ansi`
4. `./sail art storage:link`

### Rebuild `app` Container

- `./sail up --build -d` — Rebuilds the app container. There's no need to stop
  or destroy existing containers.

### Sail Commands

See [SAIL.md](SAIL.md) for a list of Sail commands.

## .gitignore and Other Ignore Files

This project uses [`ignore-sync`](https://github.com/foray1010/ignore-sync) to
help maintain ignore files and keep all of them in sync with each other. For
each `.*ignore` file there should be a corresponding `.*ignore-sync` file; e.g.,
for `.gitignore` there is a `.gitignore-sync` file. Only the `.*ignore-sync`
files should be modified. **NEVER** modify the `.*ignore` files directly.
There's also a `project.gitignore` file which is where you can place patterns
not covered by external ignore files (see
[`github/gitignore`](https://github.com/github/gitignore)). This only applies to
root ignore files (files at the root of the project directory).

To update the .*ignore files, run the following command:

```shell
./sail npm run ignore-sync
```

## Testing

### Run PHPUnit

```shell
./sail test
```

## Deployment

**_TBD_**
