# Dbackup (Laravel artisan command to backup database (Only MySQL/MariaDB))

**Dbackup** is a simple package for Laravel 5.7+ that add artisan command to backup database.

**Note** This package makes use of the **mysqldump** function through PHP **passthru**. will only work on compatible systems


## Installation

```
composer require jvizcaya/dbackup
```

We publish the config file if we want to change the storage path of the database backup and other settings.

```
php artisan vendor:publish --provider="Jvizcaya\Dbackup\DbackupServiceProvider"
```

By default the path **storage/app/backup** is used. **Make sure this directory exists** and has the necessary write permissions.

## Use mode

```
php artisan dbackup:generate
```

The command saves a database backup in the storage path, and delete the backups older (one week by default)

You can add the command **dbackup:generate** to [Task scheduling](https://laravel.com/docs/8.x/scheduling) to make database backup periodically.  

---

## License

[MIT](LICENSE) © Jorge Vizcaya | jorgevizcayaa@gmail.com
