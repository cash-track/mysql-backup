# MySQL Backup

[![Release](https://github.com/cash-track/mysql-backup/actions/workflows/release.yml/badge.svg)](https://github.com/cash-track/mysql-backup/actions/workflows/release.yml)

A simple tool to automate backups flow for MySQL database.

## Workflow

All backups will be stored in S3 storage so you need to configure credentials in `.env` (as in `.env.example`).

By default all backup is made by `mysqldump`, compressed by `gzip` and named as `backup-{id}.sql.gz`.

Backups are unified by ID which is date of the backup in format `Y-m-d` (ex: `2021-09-01`).

Backups that are already exists in storage would be overwritten by a new backup. So no problem to made backups every hour, you would have the latest version in storage every time.

## Usage

You can use it as standalone docker image or as a PHP project.

Configure credentials:
```bash
$ cp .env.example .env
```

### Docker

Make sure you attach container to the same network of MySQL daemon.

```bash
$ docker run -d --name mysql-backup --env-file .env cashtrack/mysql-backup:latest
```

By default it starts with cron with `crontab` took from the project root. Rebuild container if you need to change it.

To run commands:
```bash
$ docker run -d --name mysql-backup --env-file .env cashtrack/mysql-backup:latest php /app/app.php {command-with-arguments}
```

### PHP

Requirements:
- PHP 7.3+
- Composer
- MySQL client (`mysql` and `mysqldump`)

```bash
$ composer install
```

Then you can use simple CLI tool:
```bash
$ php app.php --help
```

### Available commands

```bash
$ php app.php list - Output a list of all backups in S3
$ php app.php backup - Make backup manually using today's date
$ php app.php restore 2019-03-01 - Restore backup with the given ID
$ php app.php clear --days=7 - Remove backups older than `--days` amount of days
```
