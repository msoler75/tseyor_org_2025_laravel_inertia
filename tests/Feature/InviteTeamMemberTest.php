<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Mail\EquipoInvitation;
use Tests\TestCase;

class InviteEquipoMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_team_members_can_be_invited_to_team(): void
    {
        if (! Features::sendsEquipoInvitations()) {
            $this->markTestSkipped('Equipo invitations not enabled.');

            return;
        }

        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $response = $this->post('/equipos/'.$user->currentEquipo->id.'/members', [
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        Mail::assertSent(EquipoInvitation::class);

        $this->assertCount(1, $user->currentEquipo->fresh()->teamInvitations);
    }

    public function test_team_member_invitations_can_be_cancelled(): void
    {
        if (! Features::sendsEquipoInvitations()) {
            $this->markTestSkipped('Equipo invitations not enabled.');

            return;
        }

        Mail::fake();

        $this->actingAs($user = User::factory()->withPersonalEquipo()->create());

        $invitation = $user->currentEquipo->teamInvitations()->create([
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        $response = $this->delete('/team-invitations/'.$invitation->id);

        $this->assertCount(0, $user->currentEquipo->fresh()->teamInvitations);
    }
}
