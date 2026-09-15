---
name: bontrouver-builder
description: >-
  Expert Laravel MVC-S system builder, CRUD generator, and automated feature tester
  tailored specifically for the Bon Trouver platform. Use whenever building new systems,
  creating CRUD modules, writing business services, or creating automated tests.
---

# Bon Trouver System Builder, CRUD & Feature Testing Skill

This skill governs the end-to-end development workflow for **Bon Trouver**. When building any new feature, CRUD module, or backend logic, follow this exact step-by-step protocol.

---

## 1. Core Architecture Flow (MVC-S)

Never place database queries, third-party calls, or heavy domain logic inside Controllers.

```text
HTTP Request
     ↓
Route (`routes/web.php` or `routes/api.php`)
     ↓
Form Request Validation (`app/Http/Requests/`)
     ↓
Thin Controller (`app/Http/Controllers/`)
     ↓
Dedicated Service Layer (`app/Services/`)
     ↓
Eloquent Model & Database Query (`app/Models/`)
     ↓
View (Blade) / JSON Resource / Event Dispatch
```

---

## 2. Step-by-Step Feature & CRUD Implementation Protocol

When instructed to create a new module or system (e.g., `Companionship`, `SmartAlert`, `Transaction`, `Review`, `PointSystem`):

### Step 1: Migration & Model
1. Define migrations with proper column types, foreign key constraints, indexes on search/filter fields, and soft deletes where appropriate.
2. Define the Model in `app/Models/` with:
   - `$fillable` array for mass assignment security.
   - `$casts` for dates, decimals, booleans, and integers.
   - Accurate relationship methods (`hasMany`, `belongsTo`, `belongsToMany`, `morphTo`).
3. **Mandatory Sync**: Immediately update `AGENTS.md` and `doc/` with the new schema and relationships.

### Step 2: Dedicated Service Layer (`app/Services/`)
1. Create a focused service class (e.g., `app/Services/SmartAlertService.php`, `app/Services/PointTransactionService.php`).
2. Implement atomic, single-responsibility methods:
   - `create(...)`, `update(...)`, `delete(...)`, `paginate(...)`, `matchAlerts(...)`, `awardPoints(...)`.
3. Wrap multi-table operations inside `DB::transaction(function () { ... })`.

### Step 3: Form Request Validation (`app/Http/Requests/`)
1. Create dedicated Form Requests for `Store...Request` and `Update...Request`.
2. Define explicit validation rules, custom error messages, and authorization logic (`authorize(): bool`).

### Step 4: Thin Controller (`app/Http/Controllers/`)
1. Inject the service class into the controller method or constructor.
2. Delegate execution strictly to the service:
   ```php
   public function store(StoreListingRequest $request, ListingService $service)
   {
       $listing = $service->create($request->validated(), auth()->user());
       return redirect()->route('listings.show', $listing)->with('success', 'Listing published successfully!');
   }
   ```

### Step 5: Seeder & Seed Verification
1. Create or update the corresponding Seeder class in `database/seeders/`.
2. Register the seeder in `DatabaseSeeder.php` in correct dependency order.

---

## 3. Automated Feature & Unit Testing Protocol

Every new CRUD module or business logic system MUST have automated feature tests written in `tests/Feature/`.

### Test File Location & Naming
- Path: `tests/Feature/<ModuleName>Test.php` (e.g., `tests/Feature/SmartAlertTest.php`, `tests/Feature/PointSystemTest.php`, `tests/Feature/CompanionshipTest.php`).

### Essential Test Cases to Cover for Every Feature:
1. **Authentication & Authorization**:
   - Guest users cannot create/modify/delete resources.
   - Users cannot edit or delete another user's resources without admin permissions.
2. **Validation Rules**:
   - Required fields fail validation when omitted.
   - Invalid formats (e.g., wrong postal code format, negative price) trigger validation errors.
3. **Successful Execution**:
   - Successful creation creates a record in the database (`assertDatabaseHas`).
   - Successful update modifies the existing record.
   - Successful deletion removes the record or soft-deletes it (`assertSoftDeleted`).
4. **Domain Logic Verification**:
   - Point awards calculate and update user's `community_points` correctly.
   - Smart alert matcher triggers when a listing matches criteria.
   - Companionship headcount limit restricts attendees when full.

### Example Feature Test Template:
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_smart_alert(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('alerts.store'), [
            'name'      => 'Montreal Room Alert',
            'keyword'   => 'Furnished',
            'city'      => 'Montréal',
            'max_price' => 800,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('smart_alerts', [
            'user_id'   => $user->id,
            'city'      => 'Montréal',
            'max_price' => 800,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_alert(): void
    {
        $response = $this->post(route('alerts.store'), [
            'name' => 'Unauthorized Alert',
        ]);

        $response->assertRedirect(route('login'));
    }
}
```

### Running Tests
Execute test suites via terminal:
```bash
php artisan test --filter=ModuleNameTest
```

---

## 4. UI Collaboration Standard
When building user interfaces, you will collaborate with the user:
- Backend data structures, dynamic blade views, and APIs will be clean and semantic.
- The user will guide and refine the visual presentation, styling, and design specifics.
