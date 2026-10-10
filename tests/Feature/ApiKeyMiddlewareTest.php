<?php

namespace Tests\Feature;

use App\Models\Integration;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiKeyMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('integrations')) {
            Schema::create('integrations', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('api_key')->nullable();
                $table->string('status')->default('1');
                $table->tinyInteger('no_expire')->default(0);
                $table->date('expiry_date')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('golf_courses')) {
            Schema::create('golf_courses', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        Integration::truncate();
    }

    /**
     * Test HTTP request with a valid API key passes authentication.
     */
    public function test_http_request_with_valid_api_key_succeeds()
    {
        Integration::create([
            'name' => 'Valid Partner',
            'api_key' => 'valid-secret-key-100',
            'status' => '1',
            'no_expire' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer valid-secret-key-100',
        ])->getJson('/api/integrations/golfcourses');

        $response->assertStatus(200);
    }

    /**
     * Test HTTP request with an invalid API key is rejected (401).
     */
    public function test_http_request_with_invalid_api_key_returns_401()
    {
        Integration::create([
            'name' => 'Valid Partner',
            'api_key' => 'valid-secret-key-100',
            'status' => '1',
            'no_expire' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-secret-key-999',
        ])->getJson('/api/integrations/golfcourses');

        $response->assertStatus(401);
    }

    /**
     * Test HTTP request with an expired token (when no_expire is 0) returns 401.
     */
    public function test_http_request_with_expired_api_key_returns_401()
    {
        Integration::create([
            'name' => 'Expired Partner',
            'api_key' => 'expired-key-200',
            'status' => '1',
            'no_expire' => 0,
            'expiry_date' => Carbon::yesterday()->format('Y-m-d'),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer expired-key-200',
        ])->getJson('/api/integrations/golfcourses');

        $response->assertStatus(401);
    }

    /**
     * Test HTTP request with a soft-deleted integration record returns 401.
     */
    public function test_http_request_with_soft_deleted_integration_returns_401()
    {
        $integration = Integration::create([
            'name' => 'Deleted Partner',
            'api_key' => 'deleted-key-300',
            'status' => '1',
            'no_expire' => 1,
        ]);

        $integration->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer deleted-key-300',
        ])->getJson('/api/integrations/golfcourses');

        $response->assertStatus(401);
    }

    /**
     * Test HTTP request with a disabled integration status (status = '0') returns 401.
     */
    public function test_http_request_with_disabled_status_returns_401()
    {
        Integration::create([
            'name' => 'Disabled Partner',
            'api_key' => 'disabled-key-400',
            'status' => '0',
            'no_expire' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer disabled-key-400',
        ])->getJson('/api/integrations/golfcourses');

        $response->assertStatus(401);
    }

    /**
     * Test HTTP request missing Authorization header returns 401.
     */
    public function test_http_request_missing_authorization_header_returns_401()
    {
        $response = $this->getJson('/api/integrations/golfcourses');
        $response->assertStatus(401);
    }

    /**
     * Test HTTP request with empty Bearer token returns 401.
     */
    public function test_http_request_with_empty_bearer_token_returns_401()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ',
        ])->getJson('/api/integrations/golfcourses');

        $response->assertStatus(401);
    }

    /**
     * Test HTTP request with malformed Authorization header returns 401.
     */
    public function test_http_request_with_malformed_authorization_header_returns_401()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Basic dXNlcjpwYXNz',
        ])->getJson('/api/integrations/golfcourses');

        $response->assertStatus(401);
    }
}
