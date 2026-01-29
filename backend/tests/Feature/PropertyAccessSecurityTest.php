<?php

namespace Tests\Feature;

use App\Models\Org;
use App\Models\Property;
use App\Models\PropertyUserRole;
use App\Models\User;
use App\Services\PropertyAccess\PropertyAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PropertyAccessSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected Org $org;
    protected User $owner;
    protected User $manager;
    protected User $staff;
    protected Property $property1;
    protected Property $property2;
    protected Property $property3;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);

        // Create organization
        $this->org = Org::factory()->create();

        // Create users
        $this->owner = User::factory()->create([
            'org_id' => $this->org->id,
            'email' => 'owner@test.com',
        ]);
        $this->owner->assignRole('OWNER');

        $this->manager = User::factory()->create([
            'org_id' => $this->org->id,
            'email' => 'manager@test.com',
        ]);
        $this->manager->assignRole('MANAGER');

        $this->staff = User::factory()->create([
            'org_id' => $this->org->id,
            'email' => 'staff@test.com',
        ]);
        $this->staff->assignRole('STAFF');

        // Create properties
        $this->property1 = Property::factory()->create([
            'org_id' => $this->org->id,
            'name' => 'Property 1',
            'code' => 'P001',
        ]);

        $this->property2 = Property::factory()->create([
            'org_id' => $this->org->id,
            'name' => 'Property 2',
            'code' => 'P002',
        ]);

        $this->property3 = Property::factory()->create([
            'org_id' => $this->org->id,
            'name' => 'Property 3',
            'code' => 'P003',
        ]);

        // Assign staff to property1 and property2 only (NOT property3)
        PropertyUserRole::factory()->create([
            'org_id' => $this->org->id,
            'property_id' => $this->property1->id,
            'user_id' => $this->staff->id,
        ]);

        PropertyUserRole::factory()->create([
            'org_id' => $this->org->id,
            'property_id' => $this->property2->id,
            'user_id' => $this->staff->id,
        ]);

        // Assign manager to all properties
        PropertyUserRole::factory()->create([
            'org_id' => $this->org->id,
            'property_id' => $this->property1->id,
            'user_id' => $this->manager->id,
        ]);

        PropertyUserRole::factory()->create([
            'org_id' => $this->org->id,
            'property_id' => $this->property2->id,
            'user_id' => $this->manager->id,
        ]);

        PropertyUserRole::factory()->create([
            'org_id' => $this->org->id,
            'property_id' => $this->property3->id,
            'user_id' => $this->manager->id,
        ]);
    }

    /** @test */
    public function owner_can_access_all_org_properties(): void
    {
        $service = app(PropertyAccessService::class);
        $allowedIds = $service->allowedPropertyIds($this->owner);

        $this->assertCount(3, $allowedIds);
        $this->assertContains($this->property1->id, $allowedIds);
        $this->assertContains($this->property2->id, $allowedIds);
        $this->assertContains($this->property3->id, $allowedIds);
    }

    /** @test */
    public function manager_can_access_assigned_properties(): void
    {
        $service = app(PropertyAccessService::class);
        $allowedIds = $service->allowedPropertyIds($this->manager);

        $this->assertCount(3, $allowedIds);
        $this->assertContains($this->property1->id, $allowedIds);
        $this->assertContains($this->property2->id, $allowedIds);
        $this->assertContains($this->property3->id, $allowedIds);
    }

    /** @test */
    public function staff_can_only_access_assigned_properties(): void
    {
        $service = app(PropertyAccessService::class);
        $allowedIds = $service->allowedPropertyIds($this->staff);

        $this->assertCount(2, $allowedIds);
        $this->assertContains($this->property1->id, $allowedIds);
        $this->assertContains($this->property2->id, $allowedIds);
        $this->assertNotContains($this->property3->id, $allowedIds);
    }

    /** @test */
    public function staff_cannot_access_unassigned_property_via_api(): void
    {
        Sanctum::actingAs($this->staff);

        // Try to access property3 which staff is NOT assigned to
        $response = $this->getJson("/api/properties/{$this->property3->id}");

        // Should be forbidden or not found (403 or 404)
        $this->assertContains($response->status(), [403, 404]);
    }

    /** @test */
    public function staff_can_access_assigned_property_via_api(): void
    {
        Sanctum::actingAs($this->staff);

        // Access property1 which staff IS assigned to
        $response = $this->getJson("/api/properties/{$this->property1->id}");

        $response->assertOk();
    }

    /** @test */
    public function property_list_is_filtered_by_assigned_properties(): void
    {
        Sanctum::actingAs($this->staff);

        $response = $this->getJson('/api/properties');

        $response->assertOk();
        $data = $response->json('data');

        // Staff should only see 2 properties
        $this->assertCount(2, $data);
        $propertyIds = collect($data)->pluck('id')->toArray();
        $this->assertContains($this->property1->id, $propertyIds);
        $this->assertContains($this->property2->id, $propertyIds);
        $this->assertNotContains($this->property3->id, $propertyIds);
    }

    /** @test */
    public function cache_is_invalidated_after_property_assignment_change(): void
    {
        $service = app(PropertyAccessService::class);

        // Get initial allowed properties for staff
        $initialIds = $service->allowedPropertyIds($this->staff);
        $this->assertCount(2, $initialIds);
        $this->assertNotContains($this->property3->id, $initialIds);

        // Assign staff to property3
        PropertyUserRole::factory()->create([
            'org_id' => $this->org->id,
            'property_id' => $this->property3->id,
            'user_id' => $this->staff->id,
        ]);

        // Clear cache
        $service->clearUser($this->staff);

        // Get new allowed properties
        $newIds = $service->allowedPropertyIds($this->staff);
        $this->assertCount(3, $newIds);
        $this->assertContains($this->property3->id, $newIds);
    }

    /** @test */
    public function cross_org_property_access_is_blocked(): void
    {
        // Create another org with property
        $otherOrg = Org::factory()->create();
        $otherProperty = Property::factory()->create([
            'org_id' => $otherOrg->id,
        ]);
        $otherStaff = User::factory()->create([
            'org_id' => $otherOrg->id,
        ]);
        $otherStaff->assignRole('STAFF');

        PropertyUserRole::factory()->create([
            'org_id' => $otherOrg->id,
            'property_id' => $otherProperty->id,
            'user_id' => $otherStaff->id,
        ]);

        $service = app(PropertyAccessService::class);

        // Our staff should NOT have access to other org's property
        $allowedIds = $service->allowedPropertyIds($this->staff);
        $this->assertNotContains($otherProperty->id, $allowedIds);

        // Even if we try via API
        Sanctum::actingAs($this->staff);
        $response = $this->getJson("/api/properties/{$otherProperty->id}");
        $this->assertContains($response->status(), [403, 404]);
    }

    /** @test */
    public function staff_cannot_guess_property_ids_across_organizations(): void
    {
        // Create another org with sequential IDs
        $otherOrg = Org::factory()->create();
        $otherProperties = Property::factory()->count(5)->create([
            'org_id' => $otherOrg->id,
        ]);

        Sanctum::actingAs($this->staff);

        // Try to access each other org's property by ID guessing
        foreach ($otherProperties as $otherProperty) {
            $response = $this->getJson("/api/properties/{$otherProperty->id}");
            $this->assertContains(
                $response->status(),
                [403, 404],
                "Staff should not access property {$otherProperty->id} from another org"
            );
        }
    }

    /** @test */
    public function property_access_service_returns_cached_results(): void
    {
        $service = app(PropertyAccessService::class);

        // First call - should cache
        $firstCall = $service->allowedPropertyIds($this->staff);

        // Check cache exists
        $cacheKey = "user:{$this->staff->id}:allowed_properties";
        $this->assertTrue(Cache::has($cacheKey));

        // Second call - should use cache
        $secondCall = $service->allowedPropertyIds($this->staff);

        $this->assertEquals($firstCall, $secondCall);
    }

    /** @test */
    public function middleware_injects_scoped_property_ids_into_request(): void
    {
        Sanctum::actingAs($this->staff);

        $response = $this->getJson('/api/properties');

        // After middleware, config should have scoped_property_ids
        $scopedIds = config('app.scoped_property_ids');
        $this->assertNotNull($scopedIds);
        $this->assertIsArray($scopedIds);
        $this->assertCount(2, $scopedIds);
    }
}
