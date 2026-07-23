<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Facade;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        Facade::clearResolvedInstances();

        parent::tearDown();
    }
}
