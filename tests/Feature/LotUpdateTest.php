<?php

namespace Tests\Feature;

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
        });
    }

    public function test_logged_in_user_can_set_the_ciff_model_for_a_lot(): void
    {
        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-001',
        ]);

        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->post('/lotti', [
                'lot_number' => 'LOT-001',
                'ciff_model' => 'internorid.ciff',
            ])
            ->assertRedirect('/lotti');

        $this->assertDatabaseHas('impegnolotti', [
            'DBlotto' => 'LOT-001',
            'DBmodelli' => 'internorid.ciff',
        ]);
    }

    public function test_setting_a_lot_again_updates_its_model(): void
    {
        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-001',
            'DBmodelli' => 'internorid.ciff',
        ]);
        DB::table('impegnolotti')->insert([
            'DBlotto' => 'LOT-001',
            'DBmodelli' => null,
        ]);

        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->post('/lotti', [
                'lot_number' => 'LOT-001',
                'ciff_model' => 'enteropluri.ciff',
            ]);

        $this->assertDatabaseCount('impegnolotti', 2);
            $this->assertSame(2, DB::table('impegnolotti')
                ->where('DBlotto', 'LOT-001')
                ->where('DBmodelli', 'enteropluri.ciff')
                ->count());
    }

    public function test_unknown_lot_is_not_created(): void
    {
        $this->withSession(['set_modelli_user' => ['username' => 'admin.liof']])
            ->post('/lotti', [
                'lot_number' => 'MISSING-LOT',
                'ciff_model' => 'internorid.ciff',
            ])
            ->assertSessionHasErrors('lot_number');

        $this->assertDatabaseCount('impegnolotti', 0);
    }
}