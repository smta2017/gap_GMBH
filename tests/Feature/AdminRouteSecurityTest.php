<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyType;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminRouteSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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

        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('type')->nullable();
                $table->string('view_type')->nullable();
                $table->boolean('active')->default(1);
                $table->string('icon')->nullable();
                $table->string('icon_name')->nullable();
                $table->integer('font_type')->default(1);
                $table->integer('sorted')->default(0);
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('images')) {
            Schema::create('images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('imageable_id')->nullable();
                $table->string('imageable_type')->nullable();
                $table->string('file_name')->nullable();
                $table->integer('rank')->default(1);
                $table->timestamps();
            });
        }
    }

    protected function createNonAdminUser()
    {
        $user = User::create([
            'email' => 'client@example.com',
            'password' => bcrypt('password'),
        ]);

        $company = Company::create([
            'name' => 'Client Agency',
            'company_type_id' => CompanyType::TA,
        ]);

        UserDetails::create([
            'user_id' => $user->id,
            'role_id' => UserDetails::CLIENT_ROLE,
            'company_id' => $company->id,
        ]);

        return $user;
    }

    protected function createAdminUser()
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

        return $user;
    }

    /**
     * Test unauthenticated access to administrative endpoints returns 401.
     */
    public function test_unauthenticated_requests_to_admin_routes_are_rejected()
    {
        $adminEndpoints = [
            ['GET', '/davinci'],
            ['GET', '/services'],
            ['POST', '/save-service'],
            ['POST', '/new-service'],
            ['GET', '/facility'],
            ['POST', '/save-facility'],
            ['GET', '/field'],
            ['POST', '/save-field'],
            ['GET', '/room-field'],
            ['POST', '/save-room-field'],
            ['GET', '/difficulty'],
            ['POST', '/save-difficulty'],
            ['GET', '/golf-style'],
            ['POST', '/save-golf-style'],
            ['GET', '/show-log'],
            ['GET', '/get-log'],
            ['POST', '/clear-log'],
            ['POST', '/reset-rank'],
            ['GET', '/pulk_publish'],
            ['POST', '/pulk_publish'],
        ];

        foreach ($adminEndpoints as [$method, $uri]) {
            $response = $this->json($method, $uri);
            $response->assertStatus(401);
        }
    }

    /**
     * Test authenticated non-admin users are forbidden (403).
     */
    public function test_non_admin_authenticated_users_are_forbidden()
    {
        $nonAdmin = $this->createNonAdminUser();
        $this->actingAs($nonAdmin);

        $adminEndpoints = [
            ['GET', '/services'],
            ['POST', '/save-service'],
            ['GET', '/facility'],
            ['GET', '/show-log'],
            ['POST', '/clear-log'],
            ['POST', '/reset-rank'],
        ];

        foreach ($adminEndpoints as [$method, $uri]) {
            $response = $this->json($method, $uri);
            $response->assertStatus(403);
        }
    }

    /**
     * Test authorized admin users can access administrative endpoints.
     */
    public function test_authorized_admin_users_can_access_admin_routes()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $response1 = $this->get('/services');
        $response1->assertStatus(200);

        $response2 = $this->get('/show-log');
        $response2->assertStatus(200);

        $response3 = $this->postJson('/reset-rank');
        $response3->assertStatus(200);
    }

    /**
     * Test log viewing prevents path traversal attempts.
     */
    public function test_log_viewing_prevents_path_traversal()
    {
        $admin = $this->createAdminUser();
        $this->actingAs($admin);

        $response = $this->get('/get-log?filename=../../.env');
        $response->assertStatus(404);
    }

    /**
     * Test public destructive routes remain completely absent (404).
     */
    public function test_public_destructive_operations_are_absent()
    {
        $this->get('/delete-logfiles')->assertStatus(404);
        $this->get('/wright-files-is-log')->assertStatus(404);
        $this->get('/api/sql')->assertStatus(404);
    }
}
