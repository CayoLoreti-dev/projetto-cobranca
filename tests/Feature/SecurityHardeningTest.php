<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use ReflectionProperty;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_applied_to_web_and_api_responses(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Content-Security-Policy', "base-uri 'self'; object-src 'none'; frame-ancestors 'self'");

        $this->getJson('/api/v1/clientes')
            ->assertUnauthorized()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Content-Security-Policy', "base-uri 'self'; object-src 'none'; frame-ancestors 'self'");
    }

    public function test_token_endpoint_is_rate_limited_by_email_and_ip(): void
    {
        config(['security.rate_limits.api_token_per_minute' => 2]);

        $payload = [
            'email' => 'rate-limit@example.com',
            'password' => 'wrong-password',
            'device_name' => 'test-device',
        ];

        $this->postJson('/api/v1/tokens', $payload)->assertUnprocessable();
        $this->postJson('/api/v1/tokens', $payload)->assertUnprocessable();

        $this->postJson('/api/v1/tokens', $payload)->assertTooManyRequests();
    }

    public function test_default_password_policy_requires_a_strong_password(): void
    {
        $weak = Validator::make(['password' => 'cobranca123'], [
            'password' => [Password::defaults()],
        ]);

        $strong = Validator::make(['password' => 'Cobranca!2026'], [
            'password' => [Password::defaults()],
        ]);

        $this->assertTrue($weak->fails());
        $this->assertFalse($strong->fails());
    }

    public function test_user_mfa_fields_are_encrypted_at_rest(): void
    {
        $user = User::factory()->create();

        $user->saveAppAuthenticationSecret('secret-for-tests');
        $user->saveAppAuthenticationRecoveryCodes(['recovery-for-tests']);

        $raw = DB::table('users')->where('id', $user->id)->first();
        $fresh = $user->fresh();

        $this->assertSame('secret-for-tests', $fresh->getAppAuthenticationSecret());
        $this->assertSame(['recovery-for-tests'], $fresh->getAppAuthenticationRecoveryCodes());
        $this->assertNotSame('secret-for-tests', $raw->app_authentication_secret);
        $this->assertNotSame(json_encode(['recovery-for-tests']), $raw->app_authentication_recovery_codes);
    }

    public function test_destructive_artisan_commands_are_prohibited_in_production(): void
    {
        try {
            $this->app['env'] = 'production';

            (new AppServiceProvider($this->app))->boot();

            foreach ([
                FreshCommand::class,
                RefreshCommand::class,
                ResetCommand::class,
                RollbackCommand::class,
                WipeCommand::class,
                SeedCommand::class,
                KeyGenerateCommand::class,
            ] as $commandClass) {
                $this->assertCommandIsProhibited($commandClass);
            }
        } finally {
            $this->app['env'] = 'testing';
            DB::prohibitDestructiveCommands(false);
            SeedCommand::prohibit(false);
            KeyGenerateCommand::prohibit(false);
        }
    }

    public function test_users_are_soft_deleted_to_preserve_history(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    private function assertCommandIsProhibited(string $commandClass): void
    {
        $property = new ReflectionProperty($commandClass, 'prohibitedFromRunning');
        $property->setAccessible(true);

        $this->assertTrue($property->getValue());
    }
}
