<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase {
        migrateFreshUsing as protected migrateFreshUsingTrait;
    }

    /**
     * Override migrate:fresh options for tests to avoid production confirmation.
     *
     * @return array
     */
    protected function migrateFreshUsing()
    {
        return array_merge(
            $this->migrateFreshUsingTrait(),
            ['--force' => true]
        );
    }

    /**
     * Disable CSRF middleware in tests (csrf verification is not included in test
     * requests by default) and prevent 419 page expired errors.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class
        );
    }
}
