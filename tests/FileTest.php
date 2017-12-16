<?php

use PHLAK\Stash;

class FileTest extends PHPUnit_Framework_TestCase
{
    use Cacheable;

    protected $dirPath;
    protected $stash;

    public function setUp()
    {
        $this->dirPath = __DIR__ . '/cache';
        $this->stash = new Stash\Drivers\File(function () {
            return ['dir' => $this->dirPath];
        });
    }

    public function test_it_throws_an_exception_if_initialized_without_a_dir()
    {
        $this->setExpectedException(\RuntimeException::class);

        $stash = new Stash\Drivers\File(function () {
            return [];
        });
    }

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);

        $this->assertFalse($this->stash->get('expired'));
    }

    public function test_it_uses_a_subdirectory_when_prefixed()
    {
        $stash = new Stash\Drivers\File(function () {
            return [
                'dir' => $this->dirPath,
                'prefix' => 'some_prefix'
            ];
        });

        $stash->put('prefix-dir-test', 'asdf', 5);

        $this->assertTrue(file_exists("{$this->dirPath}/some_prefix/bf0690887658afb3f729f492c5697f25f100b991.cache.php"));
    }

    public function test_it_creates_a_cache_file_with_a_php_extension()
    {
        $this->stash->put('extension-test', 'asdf', 5);

        $this->assertTrue(file_exists("{$this->dirPath}/27ab9a58aa0a5ed06a7935b9a8a8b1edf2d2ba70.cache.php"));
    }
}
