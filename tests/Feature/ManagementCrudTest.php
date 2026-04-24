<?php

namespace Tests\Feature;

use App\Models\BoothSetting;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Authenticated users can create a category.
     */
    public function test_authenticated_user_can_create_category(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Frozen Drinks',
            'description' => 'Kategori minuman dingin.',
        ]);

        $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Frozen Drinks',
        ]);
    }

    /**
     * Authenticated users can create a product.
     */
    public function test_authenticated_user_can_create_product(): void
    {
        $user = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Minuman Segar',
            'description' => 'Kategori untuk minuman segar.',
        ]);

        $response = $this->actingAs($user)->post(route('products.store'), [
            'category_id' => $category->id,
            'name' => 'Thai Tea',
            'menu_group' => 'Es',
            'price' => 12000,
            'selling_unit' => 'Gelas',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Thai Tea',
            'category_id' => $category->id,
            'menu_group' => 'Es',
            'selling_unit' => 'Gelas',
        ]);
    }

    /**
     * Authenticated users can create a cash flow record.
     */
    public function test_authenticated_user_can_create_cash_flow(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post(route('cash-flows.store'), [
            'flow_date' => now()->format('Y-m-d H:i:s'),
            'type' => 'in',
            'amount' => 150000,
            'source' => 'Modal tambahan',
            'description' => 'Tambahan modal operasional.',
        ]);

        $response->assertRedirect(route('cash-flows.index'));

        $this->assertDatabaseHas('cash_flows', [
            'user_id' => $user->id,
            'type' => 'in',
            'amount' => 150000,
        ]);
    }

    /**
     * Admin can update booth settings.
     */
    public function test_admin_can_update_booth_settings(): void
    {
        $user = User::factory()->admin()->create();
        BoothSetting::create([
            'name' => 'Kasir Booth',
            'address' => 'Area booth utama',
            'city' => 'Makassar',
            'phone' => '-',
            'logo_path' => 'images/branding/booth-logo.svg',
            'receipt_footer' => 'Terima kasih sudah belanja.',
            'receipt_paper' => '80',
        ]);

        $response = $this->actingAs($user)->put(route('booth-settings.update'), [
            'name' => 'Cemal Cemil Alma',
            'address' => 'Jl. Yos Sudarso, Sarijaya, Palaran',
            'city' => 'Kutai Kartanegara, Kaltim',
            'phone' => '08123456789',
            'receipt_footer' => 'Sampai jumpa lagi di booth kami.',
            'receipt_paper' => '58',
        ]);

        $response->assertRedirect(route('booth-settings.edit'));
        $this->assertDatabaseHas('booth_settings', [
            'name' => 'Cemal Cemil Alma',
            'receipt_paper' => '58',
        ]);
    }
}
