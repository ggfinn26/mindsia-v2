<?php

namespace Tests\Feature\System;

use App\Models\Branch;
use App\Models\Program;
use App\Services\Dashboard\WidgetDataProviderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

// Semua widget dashboard harus bisa dihitung dengan dan tanpa filter cabang. Nama relasi atau kolom yang salah (mis. jobPosting
// padahal relasinya posting, branch_id di tabel yang tak punya kolom itu) hanya meledak saat filter cabang aktif, jadi tidak terlihat tanpa filter.
class DashboardWidgetBranchFilterTest extends TestCase
{
    /** @return array<string, array{0: class-string<WidgetDataProviderInterface>}> */
    public static function widgets(): array
    {
        $cases = [];
        foreach (glob(__DIR__.'/../../../app/Services/Dashboard/Widgets/*Widget.php') as $file) {
            $class = 'App\\Services\\Dashboard\\Widgets\\'.basename($file, '.php');
            if (is_subclass_of($class, WidgetDataProviderInterface::class)) {
                $cases[class_basename($class)] = [$class];
            }
        }

        return $cases;
    }

    #[DataProvider('widgets')]
    public function test_widget_bisa_dihitung_dengan_dan_tanpa_filter_cabang(string $class): void
    {
        $branch = Branch::factory()->create();
        // Beberapa widget melakukan loop per program; tanpa program aktif query di dalamnya tidak pernah dijalankan.
        Program::factory()->create(['is_active' => true]);
        $widget = app($class);

        $this->assertIsArray($widget->getData([]));
        $this->assertIsArray($widget->getData(['branch_id' => $branch->id]));
    }
}
