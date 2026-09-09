<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Un visitante no autenticado es enviado al login.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('dashboard', absolute: false));

        $this->get('/dashboard')->assertRedirect(route('login', absolute: false));
    }
}
