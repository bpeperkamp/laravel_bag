![Build status](https://github.com/bpeperkamp/laravel_bag/actions/workflows/ci.yml/badge.svg)

# Laravel BAG

This small release based on Laravel, downloads Dutch address data from publicly available BAG data. It downloads, extracts and parses the data to a database and makes it available via a simple API.

The original idea is not mine, i just wanted to build it in a PHP/Laravel way. The software made by Bert Hubert inspired me, and also helped interpret the data. The original version is available here: [https://github.com/berthubert/bagconv](https://github.com/berthubert/bagconv)

The application is written in a mix of Dutch and English terms. It was all a bit confusing, since i think and write software in English but the source data was mostly in Dutch :-)

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

The first step will download a large zip file, the second will unzip the needed files and the third step will import the data in the database. This will take quite some time... After this is done, you can lookup any address and postalcode in the Netherlands. The number is quite large, but it is worth it. Also, you don't need to run this many times. The data changes, but not in very high frequency.

Start the application with the following command:
```
php artisan serve
```

After all is done, you can do GET requests via the following routes:

```
http://localhost:8000/api/cities
http://localhost:8000/api/cities/{identificatie}
```

Looking up a postalcode is done via these routes (POST)

```
http://localhost:8000/api/postalcode
http://localhost:8000/api/residence
```

Both these routes take the following JSON data, the addition is optional but can be necessary in some cases:

```
{"postalcode":"1071DJ", "number": 10, "addition":""}
```

Hopefully this is a useful package to some. I'm also working on a Rust version, but that's quite a learning process for now.
