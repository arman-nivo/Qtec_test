<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // The home route redirects to login, so expect 302
        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }
    
    /**
     * Test that login page loads successfully.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
    }
    
    /**
     * Test that register page loads successfully.
     */
    public function test_register_page_loads_successfully(): void
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
    }
}