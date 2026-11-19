<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\City::clearMapCache();
    }

    protected function tearDown(): void
    {
        \App\Models\City::clearMapCache();
        parent::tearDown();
    }
}
