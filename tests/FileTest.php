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

    public function test_it_returns_false_for_an_expired_item()
    {
        $this->stash->put('expired', 'qwerty', -5);

        $this->assertFalse($this->stash->get('expired'));
    }

    public function test_it_creates_a_cache_file_with_a_php_extension()
    {
        $this->stash->put('extension-test', 'asdf', 5);

        $this->assertTrue(file_exists("{$this->dirPath}/27ab9a58aa0a5ed06a7935b9a8a8b1edf2d2ba70.cache.php"));
    }
}
