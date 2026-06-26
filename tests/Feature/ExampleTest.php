<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The public homepage should render as a guest-safe landing page.
     */
    public function test_the_public_homepage_renders_successfully()
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Flaxem Support Desk');
    }
}
