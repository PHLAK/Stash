<?php

namespace PHLAK\Stash\Tests;

use PHLAK\Stash;
use PHLAK\Stash\Exceptions\FileNotFoundException;
use PHLAK\Stash\Tests\Traits\Cacheable;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FileTest extends TestCase
{
    use Cacheable;

    protected $cachePath = __DIR__ . '/cache';
    protected $stash;

    public function setUp(): void
    {
        $cachePath = $this->cachePath;

        $this->stash = new Stash\Drivers\File(function () use ($cachePath) {
            $this->setCacheDir($cachePath);
        });
    }

    public function test_it_throws_an_exception_if_initialized_without_a_dir()
    {
        $this->expectException(\RuntimeException::class);

        $stash = new Stash\Drivers\File(function () { /* ... */ });
    }

    public function test_it_throws_an_exception_when_initialized_with_a_non_existant_dir()
    {
        $this->expectException(FileNotFoundException::class);

        new Stash\Drivers\File(function () {
            $this->setCacheDir('/some/non-existent/path/');
        });
    }

    public function test_it_throws_an_exception_when_initialized_with_a_non_writable_dir()
    {
        $this->expectException(RuntimeException::class);

        new Stash\Drivers\File(function () {
            $this->setCacheDir('/root/');
        });
    }

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);

        $this->assertFalse($this->stash->get('expired'));
    }

    public function test_it_creates_a_cache_file_with_a_php_extension()
    {
        $this->stash->put('extension-test', 'asdf', 5);

        $this->assertTrue(file_exists("{$this->cachePath}/27ab9a58aa0a5ed06a7935b9a8a8b1edf2d2ba70.cache.php"));
    }
}
