<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MaterialRopTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function material_terdeteksi_dibawah_rop()
    {
        // Arrange
        $material = Material::factory()->create([
            'stok' => 10, // di bawah ROP
            'pemakaian_rata_rata' => 5,
            'lead_time' => 2,
            'safety_stock' => 5,
        ]);
        // ROP = (5 × 2) + 5 = 15

        // Act
        $result = $material->isBelowRop();

        // Assert
        $this->assertTrue(
            $material->isBelowRop(),
            'Material seharusnya terdeteksi berada di bawah ROP'
        );
    }
}
