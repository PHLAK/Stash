PHLAK/Stash
===========

[![Latest Stable Version](https://img.shields.io/packagist/v/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Total Downloads](https://img.shields.io/packagist/dt/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Author](https://img.shields.io/badge/author-Chris%20Kankiewicz-blue.svg)](https://www.ChrisKankiewicz.com)
[![License](https://img.shields.io/packagist/l/PHLAK/Stash.svg)](https://packagist.org/packages/PHLAK/Stash)
[![Build Status](https://img.shields.io/travis/PHLAK/Stash.svg)](https://travis-ci.org/PHLAK/Stash)

Simple PHP caching library -- by, [Chris Kankiewicz](https://www.ChrisKankiewicz.com)

![Stash](stash.png)

Introduction
------------

Stash is a simple PHP caching library supporting multiple, interchangable
caching back-ends.

Supported caching back-ends:

  - File Backed
  - Memcache
  - APC

Like this project? Keep me caffeinated by [making a donation](https://paypal.me/ChrisKankiewicz).

Requirements
------------

  - [PHP](https://php.net) >= 5.5

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

### File Cache

The file cache requires a config key of `dir` that points to the directory in
which you would like your cache files to be stored.

```php
$stash = Stash\Cache::make('file', ['dir' => 'path/to/cache']);
```

or

```php
$stash = new Stash\File('path/to/cache');
```

### Memcache

Memcache requires a `host` and `port` for running Memcache server.

```php
$stash = Stash\Cache::make('memcache', ['host' => 'localhost', 'port' => '12345']);
```

or

```php
$stash = new Stash\Memcache('localhost', '12345');
```

### APC

You can optionally supply a `prefix` to help prevent collisions with APC.

```php
$stash = Stash\Cache::make('apc', ['prefix' => 'foo']);
```

or

```php
$stash = new Stash\Apc('foo');
```

Usage
-----

Available functions:

    $stash->put($key, $data, $minute);
    $stash->forever($key, $data);
    $stash->get($key, $default = false);
    $stash->has($key);
    $stash->remember($key, $minutes, Closure $closure);
    $stash->rememberForever($key, Closure $closure);
    $stash->forget($key);

Troubleshooting
---------------

More info...

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
