<?php

namespace Tests\Feature;

use App\Http\Middleware\ApiKey;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\Integration;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
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

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->string('lang')->nullable();
                $table->unsignedBigInteger('player_id')->nullable();
                $table->string('device_key')->nullable();
                $table->rememberToken();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->unsignedBigInteger('company_type_id')->default(1);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_details')) {
            Schema::create('user_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->nullable();
                $table->unsignedBigInteger('role_id')->nullable();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Verify that the arbitrary SQL execution endpoint has been removed.
     */
    public function test_arbitrary_sql_endpoint_is_disabled()
    {
        $response = $this->getJson('/api/sql?0=SELECT+1');
        $response->assertStatus(404);
    }

    /**
     * Verify path traversal protection on log viewing endpoint for authenticated admin.
     */
    public function test_get_log_prevents_path_traversal()
    {
        $user = User::create([
            'email' => 'admin@golfglobe.com',
            'password' => bcrypt('password'),
        ]);

        $company = Company::create([
            'name' => 'Golf Globe GG',
            'company_type_id' => CompanyType::GG,
        ]);

        UserDetails::create([
            'user_id' => $user->id,
            'role_id' => 1,
            'company_id' => $company->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->get('/get-log?filename=../../.env');
        $response->assertStatus(404);
    }

    /**
     * Verify dangerous unauthenticated maintenance routes are disabled.
     */
    public function test_dangerous_file_maintenance_routes_are_disabled()
    {
        $response1 = $this->get('/delete-logfiles');
        $response1->assertStatus(404);

        $response2 = $this->get('/wright-files-is-log');
        $response2->assertStatus(404);
    }

    /**
     * Verify ApiKey middleware rejects invalid tokens even when no_expire integrations exist.
     */
    public function test_api_key_middleware_rejects_invalid_tokens_when_no_expire_integration_exists()
    {
        Integration::truncate();

        Integration::create([
            'name' => 'Test Integration',
            'api_key' => 'valid-secret-key-12345',
            'status' => '1',
            'no_expire' => 1,
        ]);

        $middleware = new ApiKey();

        $requestWithInvalidToken = Request::create('/api/integrations/golfcourses', 'GET');
        $requestWithInvalidToken->headers->set('Authorization', 'Bearer invalid-token-99999');

        $response = $middleware->handle($requestWithInvalidToken, function () {
            return response()->json(['status' => 'success']);
        });

        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Verify ApiKey middleware accepts valid tokens.
     */
    public function test_api_key_middleware_accepts_valid_token()
    {
        Integration::truncate();

        Integration::create([
            'name' => 'Valid Integration',
            'api_key' => 'valid-secret-key-12345',
            'status' => '1',
            'no_expire' => 1,
        ]);

        $middleware = new ApiKey();

        $requestWithValidToken = Request::create('/api/integrations/golfcourses', 'GET');
        $requestWithValidToken->headers->set('Authorization', 'Bearer valid-secret-key-12345');

        $response = $middleware->handle($requestWithValidToken, function () {
            return response()->json(['status' => 'success']);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }
}
