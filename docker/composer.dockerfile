FROM laravelsail/php74-composer:latest

RUN mkdir /.composer \
    && chmod 0777 /.composer
