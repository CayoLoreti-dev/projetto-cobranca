<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_bloqueia_cliente_sem_permissao(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/clientes')
            ->assertForbidden();
    }

    public function test_api_permite_cliente_com_permissao(): void
    {
        Permission::findOrCreate('clientes.view');
        $user = User::factory()->create();
        $user->givePermissionTo('clientes.view');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/clientes')
            ->assertOk()
            ->assertJsonStructure(['data']);
    }
}
