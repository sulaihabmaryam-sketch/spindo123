<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WmsController extends Controller
{
    public function __construct(
        protected WmsService $wmsService
    ) {}

    public function warehouseMap(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->wmsService->getWarehouseMap(),
        ]);
    }

    /**
     * Trigger manual sync from AppSheet SIKUTA
     */
    public function syncFromAppSheet(Request $request): JsonResponse
    {
        // Allow up to 5 minutes for SIKUTA API sync
        set_time_limit(300);

        try {
            // Run pending migrations to ensure columns exist on Railway
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[Auto-migrate] ' . $e->getMessage());
            }

            $appSheet = app(\App\Services\AppSheetService::class);
            // Default to fast stock sync (10s). If products empty, sync all.
            $defaultTable = \App\Models\PipeProduct::count() > 0 ? 'stok' : 'all';
            $table = $request->input('table', $defaultTable);

            // Flush cache for fresh data
            $appSheet->flushCache();

            // Run sync via artisan
            \Illuminate\Support\Facades\Artisan::call('sikuta:sync', [
                '--table' => $table,
                '--force' => true,
            ]);

            $output = \Illuminate\Support\Facades\Artisan::output();

            $totalProducts = \App\Models\PipeProduct::count();
            $totalRacks = \App\Models\WarehouseRack::count();

            $gudangCount = preg_match('/(\d+) gudang synced/', $output, $m) ? (int) $m[1] : \App\Models\WarehouseZone::count();
            $blokCount = preg_match('/(\d+) blok synced/', $output, $m) ? (int) $m[1] : $totalRacks;
            $produkCount = preg_match('/(\d+) produk synced/', $output, $m) ? (int) $m[1] : $totalProducts;
            $stokCount = preg_match('/(\d+) status stok synced/', $output, $m) ? (int) $m[1] : 0;

            // Get actual database stats after sync
            $racksWithStock = \App\Models\WarehouseRack::where('current_weight_tons', '>', 0)->count();
            $totalInventory = \App\Models\PipeInventory::count();
            $totalPcs = \App\Models\PipeInventory::sum('qty_pcs');
            $totalWeightKg = \App\Models\PipeInventory::sum('total_weight_kg');

            // Top 5 materials by stock quantity
            $topMaterials = \App\Models\PipeInventory::select(
                    'sikuta_kode_material',
                    \Illuminate\Support\Facades\DB::raw('SUM(qty_pcs) as total_pcs'),
                    \Illuminate\Support\Facades\DB::raw('SUM(total_weight_kg) as total_kg')
                )
                ->whereNotNull('sikuta_kode_material')
                ->groupBy('sikuta_kode_material')
                ->orderByDesc('total_pcs')
                ->limit(5)
                ->get()
                ->map(fn($item) => [
                    'material' => $item->sikuta_kode_material,
                    'pcs' => (int) $item->total_pcs,
                    'ton' => round($item->total_kg / 1000, 2),
                ]);

            $isSuccess = $stokCount > 0;
            $changes = \Illuminate\Support\Facades\Cache::get('sikuta_last_sync_changes');

            return response()->json([
                'status' => $isSuccess ? 'success' : 'warning',
                'message' => $isSuccess
                    ? "Sinkronisasi berhasil!"
                    : 'Sinkronisasi selesai tapi tidak ada data stok yang masuk. Periksa koneksi API SIKUTA.',
                'data' => [
                    'output' => $output,
                    'last_sync' => $appSheet->getLastSync(),
                    'changes' => $changes,
                    'detail' => [
                        'gudang_synced' => $gudangCount,
                        'blok_synced' => $blokCount,
                        'produk_synced' => $produkCount,
                        'stok_synced' => $stokCount,
                        'total_blok_terisi' => $racksWithStock,
                        'total_blok' => $totalRacks,
                        'total_jenis_material' => $totalProducts,
                        'total_inventory_record' => $totalInventory,
                        'total_stok_pcs' => (int) $totalPcs,
                        'total_tonase_ton' => round($totalWeightKg / 1000, 2),
                        'top_materials' => $topMaterials,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sync status
     */
    public function syncStatus(): JsonResponse
    {
        $appSheet = app(\App\Services\AppSheetService::class);
        $lastChanges = \Illuminate\Support\Facades\Cache::get('sikuta_last_sync_changes');

        return response()->json([
            'status' => 'success',
            'data' => [
                'last_sync' => $appSheet->getLastSync(),
                'connection' => ['connected' => true, 'mode' => 'live', 'message' => 'SIKUTA Live'],
                'mode' => 'live',
                'last_changes' => $lastChanges,
            ],
        ]);
    }
}
