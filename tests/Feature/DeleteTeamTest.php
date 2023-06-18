<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteEquipoTest extends TestCase
{
    use RefreshDatabase;

    public function test_teams_can_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $user->ownedEquipos()->save($team = Equipo::factory()->make([
            'personal_team' => false,
        ]));

        $team->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'test-role']
        );

        $response = $this->delete('/equipos/'.$team->id);

        $this->assertNull($team->fresh());
        $this->assertCount(0, $otherUser->fresh()->equipos);
    }

    public function test_personal_teams_cant_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $response = $this->delete('/equipos/'.$user->currentEquipo->id);

        $this->assertNotNull($user->currentEquipo->fresh());
    }
}
