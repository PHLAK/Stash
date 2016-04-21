Stash
=====

![Stash](stash.png)

-----

[![Latest Stable Version](https://img.shields.io/packagist/v/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Total Downloads](https://img.shields.io/packagist/dt/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Author](https://img.shields.io/badge/author-Chris%20Kankiewicz-blue.svg)](https://www.ChrisKankiewicz.com)
[![License](https://img.shields.io/packagist/l/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Build Status](https://img.shields.io/travis/PHLAK/Stash.svg)](https://travis-ci.org/PHLAK/Stash)

Simple PHP caching library -- by, [Chris Kankiewicz](https://www.ChrisKankiewicz.com)

Introduction
------------

Stash is a simple PHP caching library supporting multiple, interchangable
caching back-ends and an expressive (Laravel inspired) API.

Supported caching back-ends:

  - File Backed
  - Memcached
  - APC

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

#### APC

```php
$stash = Stash\Cache::make('apc');
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
$stash->remember($key, $minutes, fucntion() {
    // return something
});
```

or remember permanently:

```php
$stash->rememberForever($key, function() {
    // return something
});
```

Remove an item from the cache:

```php
$stash->forget($key);
```

Delete all items from the cache:

```php
$stash->flush();
```

Troubleshooting
---------------

Please report bugs to the [GitHub Issue Tracker](https://github.com/PHLAK/Stash/issues).

-----

MIT License

**Copyright (c) 2016 Chris Kankewicz <Chris@ChrisKankiewicz.com>**

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
