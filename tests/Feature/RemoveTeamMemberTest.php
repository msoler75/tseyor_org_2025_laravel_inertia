<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveEquipoMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_members_can_be_removed_from_teams(): void
    {
        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $user->currentEquipo->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $response = $this->delete('/equipos/'.$user->currentEquipo->id.'/members/'.$otherUser->id);

        $this->assertCount(0, $user->currentEquipo->fresh()->users);
    }

    public function test_only_team_owner_can_remove_team_members(): void
    {
        $user = User::factory()->withPersonalEquipo()->create();

        $user->currentEquipo->users()->attach(
            $otherUser = User::factory()->create(), ['role' => 'admin']
        );

        $this->actingAs($otherUser);

        $response = $this->delete('/equipos/'.$user->currentEquipo->id.'/members/'.$user->id);

        $response->assertStatus(403);
    }
}
