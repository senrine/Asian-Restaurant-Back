FROM debian:latest

ARG PHPVERSION=8.2
ENV PHPVERSION=${PHPVERSION}


# Apt-Utils installation
RUN apt-get --allow-releaseinfo-change update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends apt-utils

# Basic installation
RUN apt-get --allow-releaseinfo-change update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    lsb-release \
    default-mysql-client \
    gcc \
    git \
    gnupg \
    g++ \
    locales \
    make \
    msmtp \
    multipath-tools \
    nano \
    openssl \
    python-is-python3 \
    sudo \
    tar \
    unzip \
    wget \
    && rm -rf /var/lib/apt/lists/*

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
RUN echo "www ALL=(ALL) NOPASSWD:ALL" | (su -c 'EDITOR="tee" visudo -f /etc/sudoers.d/wsl')


# - Set the locale + timezone --------------------------------------------------
ENV TZ=Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \
    dpkg-reconfigure -f noninteractive tzdata && \
    locale-gen --purge en_US.UTF-8 && \
    sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    echo 'LANG="en_US.UTF-8"'>/etc/default/locale && \
    dpkg-reconfigure --frontend=noninteractive locales && \
    update-locale LANG=en_US.UTF-8

ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8
ENV LC_ALL en_US.UTF-8


# Php installation
RUN curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg \
    && sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    php$PHPVERSION \
    php$PHPVERSION-apcu \
    php$PHPVERSION-bz2 \
    php$PHPVERSION-cli \
    php$PHPVERSION-common \
    php$PHPVERSION-curl \
    php$PHPVERSION-fpm \
    php$PHPVERSION-gd \
    php$PHPVERSION-imagick \
    php$PHPVERSION-intl \
    php$PHPVERSION-ldap \
    php$PHPVERSION-mbstring \
    php$PHPVERSION-mcrypt \
    php$PHPVERSION-mysql \
    php$PHPVERSION-soap \
    php$PHPVERSION-xdebug \
    php$PHPVERSION-xml \
    php$PHPVERSION-zip \
    php$PHPVERSION-sqlite3 \
    php$PHPVERSION-pgsql \
    && rm -rf /var/lib/apt/lists/*

# Set version for PHP CLI
RUN update-alternatives --set php /usr/bin/php$PHPVERSION

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php && mv composer.phar /usr/local/bin/composer

# Install Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli


# Symbolic link to /var/www
RUN ln -s /usr/local/apache2/htdocs /var/www

# Customize Php configuration
RUN sed -i "s/user = www-data/user = 1000/g" /etc/php/$PHPVERSION/fpm/pool.d/www.conf
RUN sed -i "s/group = www-data/group = 1000/g" /etc/php/$PHPVERSION/fpm/pool.d/www.conf
RUN sed -i "s/listen = \/run\/php\/php$PHPVERSION-fpm.sock/listen = '9009'/g" /etc/php/$PHPVERSION/fpm/pool.d/www.conf
RUN ln -sf /usr/bin/msmtp /usr/sbin/sendmail

EXPOSE 9009

WORKDIR /var/www

RUN service php$PHPVERSION-fpm start

CMD php-fpm$PHPVERSION -F -R