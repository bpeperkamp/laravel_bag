# Laravel BAG

This small release based on Laravel downloads Dutch address data from publicly available BAG data. It downloads, extracts and parses the data to a database and makes it available via a simple API.

The software made by Bert Hubert inspired me to make it, and also helped interpret the data. The original version is available here: [https://github.com/berthubert/bagconv](https://github.com/berthubert/bagconv)

## Instructions

You can either install the needed PHP packages yourself, or (when on Mac/Windows) use [Herd](https://herd.laravel.com/).

Clone this repository and install the necessary packages from it's root via: ```composer install```

After this step is done, generate an application key and run a migration. The default database should be SQLite, but can be whichever you want.

```
cp .env.example .env
php artisan key:generate
php artisan migrate
```

You now have a basic application skeleton to start downloading the BAG data. The steps to do so are (in order):

```
php artisan app:get-bag-dump
php artisan app:extract-bag-files
php artisan app:process-bag-data
```

The first step will download a large zip file, the second will unzip the needed files and the third step will import the data in the database. This will take quite some time... After this is done, you can lookup any address and postalcode in the Netherlands. The number is quite large, but it is worth it. Also, you don't need to run this many times. The data changes, but not in very high frequincy.

Hopefully this is a usefull package. I'm also working on a Rust version, but that's quite a learning process for now.
