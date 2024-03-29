<p align="center">
    <img src="stash.png" alt="Stash" width="60%">
</p>

<p align="center">
    Lightweight PHP caching library • Created by <a href="https://www.ChrisKankiewicz.com">Chris Kankiewicz</a> (<a href="https://twitter.com/PHLAK">@PHLAK</a>)
</p>

<p align="center">
    <a href="https://github.com/PHLAK/Stash/discussions"><img src="https://img.shields.io/badge/Join_the-Community-7b16ff.svg?style=for-the-badge" alt="Join our Community"></a>
    <a href="https://github.com/users/PHLAK/sponsorship"><img src="https://img.shields.io/badge/Become_a-Sponsor-cc4195.svg?style=for-the-badge" alt="Become a Sponsor"></a>
    <a href="https://paypal.me/ChrisKankiewicz"><img src="https://img.shields.io/badge/Make_a-Donation-006bb6.svg?style=for-the-badge" alt="One-time Donation"></a>
    <br>
    <a href="https://packagist.org/packages/PHLAK/Stash"><img src="https://img.shields.io/packagist/v/PHLAK/Stash.svg?style=flat-square" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/PHLAK/Stash"><img src="https://img.shields.io/packagist/dt/PHLAK/Stash.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/PHLAK/Stash"><img src="https://img.shields.io/packagist/l/PHLAK/Stash.svg?style=flat-square" alt="License"></a>
    <a href="https://github.com/PHLAK/Stash/actions"><img alt="GitHub branch checks state" src="https://img.shields.io/github/checks-status/PHLAK/Stash/master?style=flat-square"></a>
</p>

--- 

Introduction
------------

Stash is a lightweight PHP caching library supporting multiple, interchangeable
caching back-ends and an expressive (Laravel inspired) API.

Supported caching back-ends:

  - **File** - File-based caching. Stores cache items as files in a directory on disk.
  - **Memcached** - High-performance, distributed memory object caching system
  - **Redis** - In-memory data structure store.
  - **APCu** - PHP's native APC User Cache.
  - **Ephemeral** - A transient, in-memory array that only exists for the lifetime of the script.

Requirements
------------

  - [PHP](https://php.net) >= 7.2

Install with Composer
---------------------

```bash
composer require phlak/stash
```

Initializing the Client
-----------------------

First, import Stash:

```php
use PHLAK\Stash;
```

Then instantiate Stash for your back-end of choice with the named constructor:

```php
$stash = Stash\Cache::file($config);
$stash = Stash\Cache::memcached($config);
$stash = Stash\Cache::redis($config);
$stash = Stash\Cache::apcu($config);
$stash = Stash\Cache::ephemeral();
```

The `$config` parameter accepts a driver-specific [closure](https://secure.php.net/manual/en/class.closure.php)
for setting configuration options for your chosen driver. Refer to the specific
documentation about each driver below for more info. Not all drivers require a
configuration function.

Alternatively you may use the `Stash\Cache::make()` factory method to
instantiate your driver.

```php
$stash = Stash\Cache::make($driver, $config);
```

The `make()` method takes two parameters. The first (`$driver`) should be one of
the following lowercase strings representing your desired caching driver.

  - `apcu`
  - `ephemeral`
  - `file`
  - `memcached`
  - `redis`

The second (`$config`) is the same driver-specific configuration closure as when
using a named constructor. Refer to the specific documentation about each driver
below for more info.

----

#### File Cache

The file cache configuration closure must call `$this->setCacheDir($path)` where
`$path` is a path to a valid directory in which your cache files will be stored.

```php
$stash = Stash\Cache::file(function (): void {
    $this->setCacheDir('path/to/cache');
});
```

#### Memcached

The Memcached configuration closure receives an instance of the
[Memcached object](https://www.php.net/manual/en/class.memcached.php) as it's
only parameter, you can use this parameter to connect and configure Memcached.
At a minimum you must connect to one or more Memcached servers via the
`addServer()` or `addServers()` methods.

Reference the [PHP Memcached documentation](https://secure.php.net/manual/en/book.memcached.php)
for additional configuration options.

```php
$stash = Stash\Cache::memcached(function (Memcached $memcached): void {
    $memcached->addServer('localhost', 11211);
    // $memcached->setOption(Memcached::OPT_PREFIX_KEY, 'some_prefix');
});
```

#### Redis

The Redis configuration closure receives an instance of the
[Redis object](https://github.com/phpredis/phpredis#class-redis) as it's only
parameter, you can use this parameter to connect to and configure Redis. At a
minimum you must connect to one or more Redis servers via the `connect()` or
`pconnect()` methods.

Reference the [phpredis documentation](https://github.com/phpredis/phpredis#readme)
for additional configuration options.

```php
$stash = Stash\Cache::redis(function (Redis $redis): void {
    $redis->pconnect('localhost', 6379);
    // $redis->setOption(Redis::OPT_PREFIX, 'some_prefix');
});
```

#### APCu

The APCu driver caches items in PHPs APC user cache.

```php
$stash = Stash\Cache::apcu();
```

The APCu driver does not require a configuration closure. However, if you
wish to set a cache prefix you may pass a configuration closure that calls
`$this->setPrefix($prefix)` where `$prefix` is a string of your desired prefix.

```php
$stash = Stash\Cache::apcu(function (): void {
    $this->setPrefix('some_prefix');
});
```

#### Ephemeral

The Ephemeral driver caches items in a PHP array that exists in memory only for
the lifetime of the script. The Ephemeral driver does not take a configuration
closure.

```php
$stash = Stash\Cache::ephemeral();
```

Usage
-----

### `Cacheable::put( string $key , mixed $data [, $minutes = 0 ] ) : bool`

Add an item to the cache for a specified duration.

##### Examples

```php
// Cache a value for 15 minutes
$stash->put('foo', 'some value', 15);

// Cache a value indefinitely
$stash->put('bar', false);
```

---

### `Cacheable::forever( string $key , mixed $data) : bool`

Add an item to the cache permanently.

##### Examples

```php
$stash->forever('foo', 'some value');
```

---

### `Cacheable::get( string $key [, $default = false ] ) : mixed`

Retrieve an item from the cache.

##### Examples

```php
$stash->get('foo');

// Return 'default' if 'bar' doesn't exist
$stash->get('bar', 'default');
```

---
### `Cacheable::has( string $key ) : bool`

Check if an item exists in the cache.

##### Examples

```php
$stash->has('foo');
```

---

### `Cacheable::remember( string $key , int $minutes , Closure $closure ) : mixed`

Retrieve item from cache or, when item does not exist, execute a closure. The
result of the closure is then stored in the cache for the specified duration
and returned for immediate use.

##### Examples

```php
$stash->remember('foo', 60, function() {
    return new FooClass();
});
```

---

### `Cacheable::rememberForever( string $key , Closure $closure ) : mixed`

Retrieve item from cache or, when item does not exist, execute a closure. The
result of the closure is then stored in the cache permanently.

##### Examples

```php
$stash->rememberForever('pokemon', function() {
    return new Pokemon($name, $description);
});
```

---

### `Cacheable::increment( string $key [, int $value = 1 ] ) : mixed`

Increment an integer already stored in the cache.

##### Examples

```php
// Increment by 1
$stash->increment('foo');

// Increment by 10
$stash->increment('bar', 10);
```

---

### `Cacheable::decrement( string $key [, int $value = 1 ] ) : mixed`

Decrement an integer already stored in the cache.

##### Examples

```php
 // Decrements by 1
$stash->decrement('foo');

 // Decrements by 10
$stash->decrement('bar', 10);
```

---

### `Cacheable::touch( string|array $key [, int $minutes = 0 ] ) : bool`

Extend the expiration time for an item in the cache.

##### Examples

```php
 // Extend the expiration by 5 minutes
$stash->touch('foo', 5);

 // Extend the expiration indefinitely
$stash->touch('bar');

// Extend the expiration of multiple items by 5 minutes
$stash->touch(['foo', 'bar', 'baz'], 5);
```

---

### `Cacheable::forget( string $key ) : bool`

Remove an item from the cache.

##### Examples

```php
$stash->forget('foo');
```

---

### `Cacheable::flush() : bool`

Delete all items from the cache.

##### Examples

```php
$stash->flush();
```

Changelog
---------

A list of changes can be found on the [GitHub Releases](https://github.com/PHLAK/Stash/releases) page.

Troubleshooting
---------------

For general help and support join our [GitHub Discussions](https://github.com/PHLAK/Stash/discussions) or reach out on [Twitter](https://twitter.com/PHLAK).

Please report bugs to the [GitHub Issue Tracker](https://github.com/PHLAK/Stash/issues).

Copyright
---------

This project is licensed under the [MIT License](https://github.com/PHLAK/Stash/blob/master/LICENSE).
