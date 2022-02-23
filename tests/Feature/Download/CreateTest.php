<?php

namespace Tests\Feature\Download;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\UserFactory;

class CreateTest extends TestCase
{
    use WithFaker;

    /**
     * If the user is not authenticated, the user should get redirected to the login page.
     *
     * @return void
     */
    public function test_unauthenticated_user_is_redirected_to_login_page()
    {
        $response = $this->post(route('downloads.store'), []);

        $response->assertRedirect(route('login'));
    }

    /**
     * If the user is authenticated, the user should get redirected to the dashboard page.
     *
     * @return void
     */
    public function test_authenticated_user_is_redirected_to_dashboard_page()
    {
        $user = UserFactory::new()->create();

        $response = $this->actingAs($user)->post(route('downloads.store'), [
            'url' => $this->faker->url,
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * If the user is authenticated, the model should be created.
     *
     * @return void
     */
    public function test_authenticated_user_is_created_model()
    {
        $user = UserFactory::new()->create();
        $url = $this->faker->url;

        $response = $this->actingAs($user)->post(route('downloads.store'), [
            'url' => $url,
        ]);

        $this->assertDatabaseHas('downloads', [
            'url' => $url,
            'user_id' => $user->id,
        ]);
    }

    /**
     * If the user is unauthenticated, the model should not be created.
     *
     * @return void
     */
    public function test_unauthenticated_user_is_not_created_model()
    {
        $url = $this->faker->url();

        $response = $this->post(route('downloads.store'), [
            'url' => $url,
        ]);

        $this->assertDatabaseMissing('downloads', [
            'url' => $url,
        ]);
    }
}
