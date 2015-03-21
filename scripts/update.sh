#!/bin/sh

__dir__="$(\dirname "$(\perl -e "use Cwd 'abs_path'; print abs_path('$0');")")"

cd "$__dir__/.." || exit 1

script_lock_file=update.lock

if [ -f "$script_lock_file" ]; then
  echo 'update.sh is already running.'

  exit 1
fi

touch "$script_lock_file"

remove_lock_file() {
  [ -f "$script_lock_file" ] && rm -f "$script_lock_file"
}

php_fpm_user="$(\grep -s '^user' /etc/php-fpm.d/www.conf | \cut -d' ' -f3)"

# Ensure we run the script as the same user that PHP-FPM runs as.
if [ "$(\id -nu)" != "${php_fpm_user:=nde}" ]; then
  \echo "Script must run as '$php_fpm_user' user."

  remove_lock_file

  exit 1
fi

\git fetch --progress --prune --recurse-submodules=no origin

changed_files="$(\git diff-tree -r --name-only --no-commit-id origin/master HEAD)"

artisan() {
  \php artisan "$@"
}

composer_install() {
  if \composer production; then
    \echo 'Composer: Installing production dependencies'

    \composer install -o --no-dev
  else
    \echo 'Composer: Installing dependencies'

    \composer install
  fi
}

need_to_install() {
  file="${1}"

  \echo "$changed_files" | \grep -q "$file"
}

node_install() {
  if \composer production; then
    \echo 'Node: Installing production dependencies'

    # this ideally would be `npm ci`
    \npm install
  else
    \echo 'Node: Installing dependencies'

    \npm install
  fi
}

if ! \git pull; then
  remove_lock_file

  exit 1
fi

need_to_install composer.lock && composer_install
need_to_install package-lock.json && node_install

if \composer production; then
  artisan optimize:clear
  artisan optimize
  \npm run prod
else
  \npm run dev
fi

remove_lock_file
