<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LotUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('impegnolotti', function ($table) {
            $table->string('DBlotto', 20);
            $table->string('DBmodelli', 50)->nullable();
            $table->string('set_db_modelli', 20)->nullable();
        });
    }

    public function test_logged_in_user_can_set_the_ciff_model_for_a_lot(): void
    {
        $this->travelTo(Carbon::parse('2026-07-24 14:30:00', 'Europe/Rome'));

        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-001',
        ]);

        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->post('/lotti', [
                'lot_number' => 'LOT-001',
                'ciff_model' => 'PiastraRID.ciff',
            ])
            ->assertRedirect('/lotti');

        $this->assertDatabaseHas('impegnolotti', [
            'DBlotto' => 'LOT-001',
            'DBmodelli' => 'PiastraRID.ciff',
            'set_db_modelli' => 'admin.liof 14:30',
        ]);
    }

    public function test_setting_a_lot_again_updates_its_model(): void
    {
        $this->travelTo(Carbon::parse('2026-07-24 14:30:00', 'Europe/Rome'));

        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-001',
            'DBmodelli' => 'PiastraRID.ciff',
        ]);
        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-001',
            'DBmodelli' => null,
        ]);

        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->post('/lotti', [
                'lot_number' => 'LOT-001',
                'ciff_model' => 'BustaRID.cif',
            ]);

        $this->assertDatabaseCount('impegnolotti', 2);
        $this->assertSame(2, DB::table('impegnolotti')
            ->where('DBlotto', 'LOT-001')
            ->where('DBmodelli', 'BustaRID.cif')
            ->count());
        $this->assertSame(2, DB::table('impegnolotti')
            ->where('DBlotto', 'LOT-001')
            ->where('set_db_modelli', 'admin.liof 14:30')
            ->count());
    }

    public function test_unknown_lot_is_not_created(): void
    {
        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->post('/lotti', [
                'lot_number' => 'MISSING-LOT',
                'ciff_model' => 'PiastraRID.ciff',
            ])
            ->assertSessionHasErrors('lot_number');

        $this->assertDatabaseCount('impegnolotti', 0);
    }

    public function test_current_recipe_is_returned_only_when_it_is_set(): void
    {
        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-WITH-RECIPE',
            'DBmodelli' => 'PiastraRID.ciff',
        ]);
        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-WITHOUT-RECIPE',
            'DBmodelli' => '',
        ]);

        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->getJson('/lotti/ricetta?lot_number=LOT-WITH-RECIPE')
            ->assertOk()
            ->assertExactJson(['recipe' => 'PiastraRID.ciff']);

        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->getJson('/lotti/ricetta?lot_number=LOT-WITHOUT-RECIPE')
            ->assertOk()
            ->assertExactJson(['recipe' => null]);
    }
}