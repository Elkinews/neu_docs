#!/bin/sh

__dir__="$(\dirname "$(\perl -e "use Cwd 'abs_path'; print abs_path('$0');")")"

# Ensure command runs from project root.
cd "$__dir__" || exit 1

# Determine if stdout is a terminal...
if test -t 1; then
  # Determine if colors are supported...
  ncolors=$(tput colors)

  if test -n "$ncolors" && test "$ncolors" -ge 8; then
    BOLD="$(tput bold)"
    YELLOW="$(tput setaf 3)"
    GREEN="$(tput setaf 2)"
    NC="$(tput sgr0)"
  fi
fi

ensure_docker_is_running() {
  # Ensure that Docker is running...
  if ! \docker info > /dev/null 2>&1; then
    \echo "${BOLD}Docker is not running.${NC}" >&2

    exit 1
  fi
}

docker_run() {
  image="$1"

  shift

  # docker run [OPTIONS] IMAGE [COMMAND] [ARG...]
  # -i   : Keep STDIN open even if not attached.
  # --rm : Automatically remove the container when it exits.
  # -t   : Allocate a pseudo-TTY
  # -u   : Username or UID (format: <name|uid>[:<group|gid>])
  # -v   : Bind mount a volume; the local project root directory maps to the
  #        container's `/opt` directory.
  # -w   : Working directory inside the container.
  \docker run \
    -i --rm -t \
    -u "$(\id -u):$(\id -g)" \
    -v "$(\pwd):/var/www/html" \
    -w /var/www/html \
    "$image" \
    "$@"
}

docker_build_and_run() {
  image="$1"

  shift

  \docker build --no-cache -t "$image" -q - < "docker/$image.dockerfile"
  docker_run "$image" "$@"
  \docker image rm "$image"
}

if [ ! -f .env ]; then
  \cp -v .env.example .env
fi

sail="$__dir__/vendor/bin/sail"

# Ensure Sail is installed.
if ! [ -x "$sail" ]; then
  ensure_docker_is_running

  docker_build_and_run composer composer install --ignore-platform-reqs
fi

if [ ! -d node_modules ]; then
  ensure_docker_is_running

  docker_build_and_run npm npm install
fi

if [ -x "$sail" ]; then
  "$sail" "$@"
fi
