# About This Repository

This is a simple API for organize kost's data by doing CRUD.

This project using Laravel 8 with Laravel Sanctum for Authentication.

# What I Use

- PHP 7.4.3

- Laravel 8.37.0

- Laravel Sanctum for Authentication

# Documentation

Please open this [documentation link](https://documenter.getpostman.com/view/5496895/TzJsfJJ9) for how to use this API.

# Scheduling Task

This project also implement scheduled task for recharge point on the first day of every month at 00:00 named **monthly:recharge**.

To run manually the scheduling:

1. Check the task

```
    php artisan list
```

2. Run the task

```
    php artisan monthly:recharge
```

or you can use **Crontab**:

1. Open crontab file

```
    crontab -e
```

2. Edit crontab file and add

```
    0 0 1 * * cd /your-project-path && php artisan monthly:recharge >> /dev/null 2>&1
```

# How to use

1. Clone this Repo

```
    git clone https://github.com/lulu8879/simple-kost-api-laravel.git
```

2. Copy file .env.example and rename it to .env

```
    cp .env.example .env
```

3. Edit .env, fill DB setting and save

4. Migrate DB

```
    php artisan migrate
```

5. Run the project

```
    php artisan serve
```
