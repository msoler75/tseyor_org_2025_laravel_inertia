<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveEquipoTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_leave_teams(): void
    {
        $user = User::factory()->withPersonalEquipo()->create();

        $user->currentEquipo->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $response = $this->delete('/equipos/'.$user->currentEquipo->id.'/members/'.$otherUser->id);

        $this->assertCount(0, $user->currentEquipo->fresh()->users);
    }

    public function test_team_owners_cant_leave_their_own_team(): void
    {
        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $response = $this->delete('/equipos/'.$user->currentEquipo->id.'/members/'.$user->id);

        $response->assertSessionHasErrorsIn('removeEquipoMember', ['team']);

        $this->assertNotNull($user->currentEquipo->fresh());
    }
}
