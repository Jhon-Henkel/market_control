<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class FalconNine extends BaseTestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeFaker('pt_BR');
    }
}
