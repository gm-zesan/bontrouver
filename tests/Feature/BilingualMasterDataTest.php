<?php

namespace Tests\Feature;

use App\Enums\CompanionshipType;
use App\Enums\ListingStatus;
use App\Enums\ReportReason;
use App\Enums\UserRole;
use App\Models\AttributeOption;
use App\Models\BannerAd;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\City;
use App\Models\MemberTier;
use App\Models\PointRule;
use App\Models\PromotionPackage;
use App\Models\Province;
use App\Models\User;
use App\Services\AdminCategoryService;
use App\Services\AdminLocationService;
use App\Services\AdminMemberTierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BilingualMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        app()->setLocale('en');
        parent::tearDown();
    }

    public function test_category_name_and_description_accessors_return_localized_values(): void
    {
        $category = Category::create([
            'name'           => 'Vehicles',
            'name_en'        => 'Vehicles',
            'name_fr'        => 'Véhicules',
            'slug'           => 'vehicles',
            'description'    => 'Cars, trucks, and more',
            'description_en' => 'Cars, trucks, and more',
            'description_fr' => 'Voitures, camions et plus',
            'is_active'      => true,
        ]);

        app()->setLocale('en');
        $this->assertEquals('Vehicles', $category->name);
        $this->assertEquals('Cars, trucks, and more', $category->description);

        app()->setLocale('fr');
        $this->assertEquals('Véhicules', $category->name);
        $this->assertEquals('Voitures, camions et plus', $category->description);

        // Fallback test: when French is empty, return English
        $fallbackCategory = Category::create([
            'name'           => 'Electronics',
            'name_en'        => 'Electronics',
            'name_fr'        => null,
            'slug'           => 'electronics',
            'is_active'      => true,
        ]);

        app()->setLocale('fr');
        $this->assertEquals('Electronics', $fallbackCategory->name);
    }

    public function test_province_and_city_name_accessors_return_localized_values(): void
    {
        $province = Province::create([
            'name'         => 'Quebec',
            'name_en'      => 'Quebec',
            'name_fr'      => 'Québec',
            'code'         => 'QC',
            'slug'         => 'qc',
            'country_code' => 'CA',
            'is_active'    => true,
        ]);

        $city = City::create([
            'name'        => 'Montreal',
            'name_en'     => 'Montreal',
            'name_fr'     => 'Montréal',
            'slug'        => 'montreal',
            'province_id' => $province->id,
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
            'is_active'   => true,
        ]);

        app()->setLocale('en');
        $this->assertEquals('Quebec', $province->name);
        $this->assertEquals('Montreal', $city->name);

        app()->setLocale('fr');
        $this->assertEquals('Québec', $province->name);
        $this->assertEquals('Montréal', $city->name);
    }

    public function test_category_attribute_and_options_accessors_return_localized_values(): void
    {
        $category = Category::create([
            'name'      => 'Vehicles',
            'name_en'   => 'Vehicles',
            'name_fr'   => 'Véhicules',
            'slug'      => 'vehicles-attr-test',
            'is_active' => true,
        ]);

        $attribute = CategoryAttribute::create([
            'category_id' => $category->id,
            'name'        => 'transmission',
            'name_en'     => 'transmission',
            'name_fr'     => 'boite_de_vitesses',
            'slug'        => 'transmission',
            'label'       => 'Transmission',
            'label_en'    => 'Transmission',
            'label_fr'    => 'Boîte de vitesses',
            'type'        => 'select',
            'is_required' => false,
            'is_filterable' => true,
        ]);

        $option = AttributeOption::create([
            'category_attribute_id' => $attribute->id,
            'value'                 => 'Automatic',
            'label'                 => 'Automatic',
            'label_en'              => 'Automatic',
            'label_fr'              => 'Automatique',
            'sort_order'            => 1,
        ]);

        app()->setLocale('en');
        $this->assertEquals('transmission', $attribute->name);
        $this->assertEquals('Automatic', $option->label);

        app()->setLocale('fr');
        $this->assertEquals('boite_de_vitesses', $attribute->name);
        $this->assertEquals('Automatique', $option->label);
    }

    public function test_member_tier_accessors_return_localized_values(): void
    {
        $tier = MemberTier::create([
            'name'           => '🥈 Active Member',
            'name_en'        => '🥈 Active Member',
            'name_fr'        => '🥈 Membre actif',
            'icon'           => '🥈',
            'badge_color'    => '#94a3b8',
            'badge_class'    => 'tier-silver',
            'min_points'     => 100,
            'max_points'     => 299,
            'description'    => 'Reliable contributor',
            'description_en' => 'Reliable contributor',
            'description_fr' => 'Contributeur fiable',
            'perks'          => ['Priority support'],
            'perks_en'       => ['Priority support'],
            'perks_fr'       => ['Assistance prioritaire'],
        ]);

        app()->setLocale('en');
        $this->assertEquals('🥈 Active Member', $tier->name);
        $this->assertEquals('Active Member', $tier->clean_name);
        $this->assertEquals('Reliable contributor', $tier->description);
        $this->assertEquals(['Priority support'], $tier->perks);

        app()->setLocale('fr');
        $this->assertEquals('🥈 Membre actif', $tier->name);
        $this->assertEquals('Membre actif', $tier->clean_name);
        $this->assertEquals('Contributeur fiable', $tier->description);
        $this->assertEquals(['Assistance prioritaire'], $tier->perks);
    }

    public function test_point_rule_accessors_return_localized_values(): void
    {
        $rule = PointRule::create([
            'rule_key'       => 'identity_verification',
            'name'           => 'Identity Verification',
            'name_en'        => 'Identity Verification',
            'name_fr'        => 'Vérification d\'identité',
            'type'           => 'earn',
            'points'         => 50,
            'category'       => 'Trust & Safety',
            'description'    => 'Uploaded official ID document',
            'description_en' => 'Uploaded official ID document',
            'description_fr' => 'Document d\'identité officiel téléversé',
            'is_active'      => true,
            'sort_order'     => 1,
        ]);

        app()->setLocale('en');
        $this->assertEquals('Identity Verification', $rule->name);
        $this->assertEquals('Uploaded official ID document', $rule->description);

        app()->setLocale('fr');
        $this->assertEquals('Vérification d\'identité', $rule->name);
        $this->assertEquals('Document d\'identité officiel téléversé', $rule->description);
    }

    public function test_promotion_package_accessors_return_localized_values(): void
    {
        $pkg = PromotionPackage::create([
            'name'           => 'Featured Listing',
            'name_en'        => 'Featured Listing',
            'name_fr'        => 'Annonce en vedette',
            'slug'           => 'featured-test',
            'type'           => 'featured',
            'badge_text'     => 'Featured',
            'badge_text_en'  => 'Featured',
            'badge_text_fr'  => 'En vedette',
            'badge_color'    => '#3b82f6',
            'badge_icon'     => 'ri-star-line',
            'price'          => 4.99,
            'point_cost'     => 50,
            'duration_days'  => 7,
            'description'    => 'Highlight your ad',
            'description_en' => 'Highlight your ad',
            'description_fr' => 'Mettez votre annonce en valeur',
            'features'       => ['Homepage spotlight'],
            'features_en'    => ['Homepage spotlight'],
            'features_fr'    => ['Mise en avant sur l\'accueil'],
            'is_active'      => true,
            'sort_order'     => 1,
        ]);

        app()->setLocale('en');
        $this->assertEquals('Featured Listing', $pkg->name);
        $this->assertEquals('Featured', $pkg->badge_text);
        $this->assertEquals('Highlight your ad', $pkg->description);
        $this->assertEquals(['Homepage spotlight'], $pkg->features);

        app()->setLocale('fr');
        $this->assertEquals('Annonce en vedette', $pkg->name);
        $this->assertEquals('En vedette', $pkg->badge_text);
        $this->assertEquals('Mettez votre annonce en valeur', $pkg->description);
        $this->assertEquals(['Mise en avant sur l\'accueil'], $pkg->features);
    }

    public function test_banner_ad_accessors_return_localized_values(): void
    {
        $banner = BannerAd::create([
            'title'          => 'Spring Promo',
            'title_en'       => 'Spring Promo',
            'title_fr'       => 'Promo du printemps',
            'button_text_en' => 'Learn More',
            'button_text_fr' => 'En savoir plus',
            'position'       => 'homepage_top',
            'is_active'      => true,
            'sort_order'     => 1,
        ]);

        app()->setLocale('en');
        $this->assertEquals('Spring Promo', $banner->title);
        $this->assertEquals('Learn More', $banner->button_text);

        app()->setLocale('fr');
        $this->assertEquals('Promo du printemps', $banner->title);
        $this->assertEquals('En savoir plus', $banner->button_text);
    }

    public function test_enums_return_localized_labels(): void
    {
        // ListingStatus Enum
        $this->assertEquals('Active', ListingStatus::ACTIVE->label('en'));
        $this->assertEquals('Actif', ListingStatus::ACTIVE->label('fr'));
        $this->assertEquals('Pending Review', ListingStatus::PENDING_REVIEW->label('en'));
        $this->assertEquals('En attente de révision', ListingStatus::PENDING_REVIEW->label('fr'));
        $this->assertEquals('Draft', ListingStatus::DRAFT->label('en'));
        $this->assertEquals('Brouillon', ListingStatus::DRAFT->label('fr'));
        $this->assertEquals('Sold', ListingStatus::SOLD->label('en'));
        $this->assertEquals('Vendu', ListingStatus::SOLD->label('fr'));

        // ReportReason Enum
        $this->assertEquals('Spam / Advertising', ReportReason::SPAM->label('en'));
        $this->assertEquals('Pourriel / Publicité', ReportReason::SPAM->label('fr'));

        // CompanionshipType Enum
        $this->assertEquals('Coffee & Chat', CompanionshipType::COFFEE->label('en'));
        $this->assertEquals('Café et discussion', CompanionshipType::COFFEE->label('fr'));

        // UserRole Enum
        $this->assertEquals('Admin', UserRole::ADMIN->label('en'));
        $this->assertEquals('Administrateur', UserRole::ADMIN->label('fr'));
    }

    public function test_cities_map_helper_is_locale_aware(): void
    {
        City::clearMapCache();

        $province = Province::create([
            'name'         => 'Quebec',
            'name_en'      => 'Quebec',
            'name_fr'      => 'Québec',
            'code'         => 'QC',
            'slug'         => 'qc-map-test',
            'country_code' => 'CA',
            'is_active'    => true,
        ]);

        City::create([
            'name'        => 'Montreal',
            'name_en'     => 'Montreal',
            'name_fr'     => 'Montréal',
            'slug'        => 'montreal-map-test',
            'province_id' => $province->id,
            'latitude'    => 45.5017,
            'longitude'   => -73.5673,
            'is_active'   => true,
        ]);

        app()->setLocale('en');
        $mapEn = City::getCitiesMap();
        $this->assertArrayHasKey('Montreal', $mapEn);
        $this->assertEquals('Montreal', $mapEn['Montreal']['name']);
        $this->assertEquals('QC', $mapEn['Montreal']['province']);
        $this->assertEquals('Quebec', $mapEn['Montreal']['province_name']);

        app()->setLocale('fr');
        $mapFr = City::getCitiesMap();
        $this->assertArrayHasKey('Montréal', $mapFr);
        $this->assertEquals('Montréal', $mapFr['Montréal']['name']);
        $this->assertEquals('QC', $mapFr['Montréal']['province']);
        $this->assertEquals('Québec', $mapFr['Montréal']['province_name']);

        City::clearMapCache();
    }

    public function test_admin_category_service_persists_bilingual_fields(): void
    {
        $service = app(AdminCategoryService::class);

        $category = $service->createCategory([
            'name_en'        => 'Musical Instruments',
            'name_fr'        => 'Instruments de musique',
            'slug'           => 'musical-instruments',
            'description_en' => 'Guitars, pianos, drums',
            'description_fr' => 'Guitares, pianos, batteries',
            'is_active'      => true,
        ]);

        $this->assertEquals('Musical Instruments', $category->name_en);
        $this->assertEquals('Instruments de musique', $category->name_fr);

        $updated = $service->updateCategory($category, [
            'name_en'        => 'Music & Gear',
            'name_fr'        => 'Musique et équipement',
            'description_en' => 'All audio gear',
            'description_fr' => 'Tout équipement audio',
        ]);

        $this->assertEquals('Music & Gear', $updated->name_en);
        $this->assertEquals('Musique et équipement', $updated->name_fr);
    }

    public function test_admin_location_service_persists_bilingual_fields(): void
    {
        $locService = app(AdminLocationService::class);

        $province = Province::create([
            'name'         => 'Ontario',
            'name_en'      => 'Ontario',
            'name_fr'      => 'Ontario',
            'code'         => 'ON',
            'slug'         => 'on-test',
            'country_code' => 'CA',
            'is_active'    => true,
        ]);

        $city = $locService->createCity([
            'name_en'     => 'Ottawa',
            'name_fr'     => 'Ottawa (Capitale)',
            'province_id' => $province->id,
            'slug'        => 'ottawa-test',
            'latitude'    => 45.4215,
            'longitude'   => -75.6972,
            'is_active'   => true,
        ]);

        $this->assertEquals('Ottawa', $city->name_en);
        $this->assertEquals('Ottawa (Capitale)', $city->name_fr);

        $updatedCity = $locService->updateCity($city, [
            'name_en'     => 'City of Ottawa',
            'name_fr'     => 'Ville d\'Ottawa',
            'province_id' => $province->id,
        ]);

        $this->assertEquals('City of Ottawa', $updatedCity->name_en);
        $this->assertEquals('Ville d\'Ottawa', $updatedCity->name_fr);
    }
}
