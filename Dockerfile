FROM php:8.0.9-cli

RUN touch /var/log/cron.log

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      cron \
      python3 \
      python3-pip \
      python3-setuptools \
      groff \
      less \
      default-mysql-client \
      build-essential \
      nano \
      libzip-dev \
      libonig-dev \
      unzip && \
      pip3 install --upgrade pip && \
    apt-get clean

RUN pip3 --no-cache-dir install --upgrade awscli

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

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
