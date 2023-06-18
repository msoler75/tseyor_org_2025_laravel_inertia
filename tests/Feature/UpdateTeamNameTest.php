<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateEquipoNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_names_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $response = $this->put('/equipos/'.$user->currentEquipo->id, [
            'name' => 'Test Equipo',
        ]);

        $this->assertCount(1, $user->fresh()->ownedEquipos);
        $this->assertEquals('Test Equipo', $user->currentEquipo->fresh()->name);
    }
}
