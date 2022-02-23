<?php

namespace Tests\Feature\Download;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\UserFactory;
use Database\Factories\DownloadFactory;
use Illuminate\Support\Facades\Queue;

class DeleteTest extends TestCase
{
    use WithFaker;

    /**
     * A download can be deleted.
     *
     * @return void
     */
    public function test_example()
    {
        Queue::fake();

        $user = UserFactory::new()->create();
        $download = DownloadFactory::new()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('downloads.destroy', $download->id));

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('downloads', [
            'id' => $download->id,
        ]);
    }
}
