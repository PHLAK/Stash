<?php

namespace Tests\Drivers;

use PHLAK\Stash;
use PHLAK\Stash\Drivers\Ephemeral;
use PHLAK\Stash\Drivers\File;
use PHLAK\Stash\Exceptions\FileNotFoundException;
use PHLAK\Stash\Interfaces\Cacheable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\Traits\Cacheable as CacheableTrait;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

#[CoversClass(Ephemeral::class)]
class FileTest extends TestCase
{
    use CacheableTrait;

    protected string $cachePath = __DIR__ . '/../cache';
    protected Cacheable $stash;

    public function setUp(): void
    {
        $cachePath = $this->cachePath;

        $this->stash = new File(function () use ($cachePath): void {
            $this->setCacheDir($cachePath);
        });
    }

    #[Test]
    public function it_throws_an_exception_if_initialized_without_a_dir(): void
    {
        $this->expectException(RuntimeException::class);

        new Stash\Drivers\File(function (): void {
            // ...
        });
    }

    #[Test]
    public function it_throws_an_exception_when_initialized_with_a_non_existant_dir(): void
    {
        $this->expectException(FileNotFoundException::class);

        new Stash\Drivers\File(function (): void {
            $this->setCacheDir('/some/non-existent/path/');
        });
    }

    #[Test]
    public function it_throws_an_exception_when_initialized_with_a_non_writable_dir(): void
    {
        $this->expectException(RuntimeException::class);

        new Stash\Drivers\File(function (): void {
            $this->setCacheDir('/root/');
        });
    }

    #[Test]
    public function it_creates_a_cache_file_with_a_php_extension(): void
    {
        $this->stash->put('extension-test', 'asdf', 5);

        $this->assertTrue(file_exists("{$this->cachePath}/27ab9a58aa0a5ed06a7935b9a8a8b1edf2d2ba70.cache.php"));
    }
}
