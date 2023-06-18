<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateEquipoTest extends TestCase
{
    use RefreshDatabase;

    public function test_teams_can_be_created(): void
    {
        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $response = $this->post('/equipos', [
            'name' => 'Test Equipo',
        ]);

        $this->assertCount(2, $user->fresh()->ownedEquipos);
        $this->assertEquals('Test Equipo', $user->fresh()->ownedEquipos()->latest('id')->first()->name);
    }
}
