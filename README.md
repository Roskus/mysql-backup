# MySQL Backup

This simple script creates a backup of every MySQL database and compresses it.

* Load password from config file
* Export functions and triggers in .sql files
* Compress backups with gzip
* Database exception list
* Organized backups folder by date backup/YYYY/MM/DD/

## Run

```bash
php backup-db.php
```

## Contab configuration

Run every day at 12 am

```bash
* 0 0 * * * cd /var/www/cron/ && php backup-db.php
```

## Setup

* Clone the repository
* Add execution permision

```bash
chmod +x backup-db.php
```

* Copy .dist files:

```bash
cp config.php.dist config.php
cp mysql.conf.dist mysql.conf
```

* Set your user and password in config files.
* Add crontab configuration:

```bash
crontab -e
```
