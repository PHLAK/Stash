Stash
=====

![Stash](stash.png)

-----

[![Latest Stable Version](https://img.shields.io/packagist/v/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Total Downloads](https://img.shields.io/packagist/dt/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Author](https://img.shields.io/badge/author-Chris%20Kankiewicz-blue.svg)](https://www.ChrisKankiewicz.com)
[![License](https://img.shields.io/packagist/l/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Build Status](https://img.shields.io/travis/PHLAK/Stash.svg)](https://travis-ci.org/PHLAK/Stash)
[![StyleCI](https://styleci.io/repos/55566401/shield?branch=master&style=flat)](https://styleci.io/repos/55566401)

Simple PHP caching library -- by, [Chris Kankiewicz](https://www.ChrisKankiewicz.com)

Introduction
------------

Stash is a simple PHP caching library supporting multiple, interchangable
caching back-ends and an expressive (Laravel inspired) API.

Supported caching back-ends:

  - File Backed
  - Memcached
  - APCu
  - Ephemeral

Like this project? Keep me caffeinated by [making a donation](https://paypal.me/ChrisKankiewicz).

Requirements
------------

  - [PHP](https://php.net) >= 5.4

Install with Composer
---------------------

```bash
composer require phlak/stash
```

Initializing the Client
-----------------------

First, import Stash:

```php
use Stash;
```

Then instantiate the class for your back-end of choice:

#### File Cache

The file cache requires a config option of `dir` that points to the directory in
which you would like your cache files to be stored.

```php
$stash = Stash\Cache::make('file', ['dir' => 'path/to/cache']);
```

#### Memcached

Pass an array of Memcached servers via the `servers` config option. The `host`
and `port` are required, `weight` is optional and has a default value of `0`.

**Single Memcached server:**

```php
$stash = Stash\Cache::make('memcached', [
    'servers' => [
        ['host' => 'localhost', 'port' => 11211]
    ]
]);
```

**Multiple Memcached servers:**

```php
$stash = Stash\Cache::make('memcached', [
    'servers' => [
        [
            'host'   => 'server1',
            'port'   => 11211,
            'weight' => 100
        ],
        [
            'host'   => 'server2',
            'port'   => 11211,
            'weight' => 200
        ]
    ]
]);
```

#### APCu

```php
$stash = Stash\Cache::make('apcu');
```

#### Ephemeral

The Ephemeral driver caches items in a PHP array that exists in memory only for
the lifetime of the script.

```php
$stash = Stash\Cache::make('ephemeral');
```

Configuration
-------------

You can optionally supply a `prefix` string option to automatiacally prefix your
cache keys with a custom value. This helps to prevent cache collisions when
sharing the cache across multiple apps.

Example:

```php
$stash = Stash\Cache::make('file', [
    'dir'    => 'path/to/cache',
    'prefix' => 'some_prefix'
]);
```


Usage
-----

Add an item to the cache for a specified duration:

```php
$stash->put($key, $data, $minutes = 0);
```

Add an item to the cache permanently:

```php
$stash->forever($key, $data);
```

Retrieve an item from the cache:

```php
$stash->get($key, $default = false);
```

Check if an item exists in the cache:

```php
$stash->has($key);
```

Retrieve item from cache or, when item does not exist, execute a closure. The
result of the closure is then stored in the cache for the specified duration
and returned for immediate use.

```php
$stash->remember($key, $minutes, function() {
    // return something
});
```

or remember permanently:

```php
$stash->rememberForever($key, function() {
    // return something
});
```

Increment an integer:

```php
$stash->increment($key, $value = 1);
```

Decrement an integer:

```php
$stash->decrement($key, $value = 1);
```

Remove an item from the cache:

```php
$stash->forget($key);
```

Delete all items from the cache:

```php
$stash->flush();
```

Changelog
---------

A list of changes can be found on the [GitHub Releases](https://github.com/PHLAK/Stash/releases) page.

Troubleshooting
---------------

Please report bugs to the [GitHub Issue Tracker](https://github.com/PHLAK/Stash/issues).

Copyright
---------

This project is liscensed under the [MIT License](https://github.com/PHLAK/Stash/blob/master/LICENSE).
