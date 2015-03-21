FROM amazonlinux:latest

ARG NODE_VERSION=16
ARG PGSQL_VERSION=13
ARG PHP_VERSION=74
ARG WWWGROUP

WORKDIR /var/www/html

ENV TZ=UTC

RUN cp /etc/skel/.bash* /root \
  && echo -e "ZONE=\"$TZ\"\nUTC=true" > /etc/sysconfig/clock \
  && ln -fns "/usr/share/zoneinfo/$TZ" /etc/localtime

RUN yum -y erase yum-plugin-priorities \
  && yum -y install \
    amazon-linux-extras-yum-plugin \
    deltarpm \
    yum-plugin-fastestmirror \
    yum-utils \
  && amazon-linux-extras enable \
    epel \
    mariadb10.5 \
    "postgresql$PGSQL_VERSION" \
    vim \
  && yum clean metadata \
  && yum -y install \
    epel-release \
    https://rpms.remirepo.net/enterprise/remi-release-7.rpm \
  && yum-config-manager --disable 'remi-php*' \
  && yum-config-manager --enable "remi-php$PHP_VERSION" \
  && yum -y update \
  && yum -y groups install \
    'AWS Tools' \
    'Development Tools' \
  && yum -y install \
    glances \
    iputils \
    libpng-devel \
    php \
    php-bcmath \
    php-cli \
    php-common \
    php-devel \
    php-fpm \
    php-gd \
    php-imap \
    php-intl \
    php-ldap \
    php-mbstring \
    php-mysqlnd \
    php-opcache \
    php-pdo \
    php-pear \
    php-pecl-igbinary \
    php-pecl-memcached \
    php-pecl-msgpack \
    php-pecl-pcov \
    php-pecl-redis5 \
    php-pecl-swoole4 \
    php-pecl-xdebug3 \
    php-pgsql \
    php-process \
    php-soap \
    php-sodium \
    php-xml \
    procps-ng \
    supervisor \
    vim-enhanced \
    vim-minimal \
    wget \
  && php -r 'readfile("https://getcomposer.org/installer");' | php -- --install-dir=/usr/bin --filename=composer \
  && curl -fsSL "https://rpm.nodesource.com/setup_$NODE_VERSION.x" | bash - \
  && yum -y install \
    mariadb \
    nodejs \
    postgresql \
  && mkdir /.cache /.composer /.node /.npm /.yarn \
  && touch /.yarnrc \
  && chmod 0777 /.cache /.composer /.node /.npm /.yarn /.yarnrc \
  && corepack enable \
  && npm install -g npm \
  && pushd /tmp \
  && wget https://github.com/ncopa/su-exec/archive/refs/tags/v0.2.tar.gz \
  && tar -vxzf v0.2.tar.gz \
  && cd su-exec-0.2 \
  && make \
  && mv su-exec /usr/bin/su-exec \
  && popd \
  && yum clean all \
  && rm -fr /var/cache/yum /var/tmp/* /tmp/*

RUN setcap 'cap_net_bind_service=+ep' /usr/bin/php

RUN groupadd --force -g "$WWWGROUP" sail
RUN useradd -ms /bin/bash --no-user-group -g "$WWWGROUP" -u 1337 sail

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
