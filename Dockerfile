FROM php:8.4.27-cli

RUN touch /var/log/cron.log

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      cron \
      awscli \
      groff \
      less \
      default-mysql-client \
      build-essential \
      nano \
      libzip-dev \
      libonig-dev \
      unzip && \
    apt-get clean

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# MariaDB 11.4+ client verifies server cert by default; MySQL server uses a self-signed one
RUN printf '[client]\nssl-verify-server-cert=0\n' > /etc/mysql/conf.d/server-cert.cnf

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN mkdir -p /app

WORKDIR /app

COPY composer.json /app
COPY composer.lock /app

RUN composer install --ignore-platform-reqs --no-scripts

COPY . /app

RUN cat /app/crontab >> /etc/crontab && \
    chmod +x /app/entrypoint && \
    chmod +x /app/cron.sh

ENTRYPOINT ["sh", "/app/entrypoint"]

CMD cron && tail -f /var/log/cron.log
