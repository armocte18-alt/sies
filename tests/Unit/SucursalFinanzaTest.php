<?php

namespace Tests\Unit;

use App\Models\SucursalFinanza;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SucursalFinanzaTest extends TestCase
{
    #[Test]
    public function balance_is_ingreso_estimado_minus_gasto_total(): void
    {
        $finanzas = new SucursalFinanza([
            'ingreso_estimado' => 280732.64,
            'gasto_total' => 359950.28,
        ]);

        $this->assertEquals(-79217.64, $finanzas->balance);
        $this->assertSame('deficitaria', $finanzas->estatus_financiero);
    }

    #[Test]
    public function positive_balance_is_superavitaria(): void
    {
        $finanzas = new SucursalFinanza([
            'ingreso_estimado' => 1000,
            'gasto_total' => 400,
        ]);

        $this->assertEquals(600, $finanzas->balance);
        $this->assertSame('superavitaria', $finanzas->estatus_financiero);
    }
}
