<?php

namespace Database\Seeders;

use App\Models\PipeCategory;
use App\Models\PipeInventory;
use App\Models\PipeProduct;
use App\Models\User;
use App\Models\WarehouseRack;
use App\Models\WarehouseZone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin User ───
        User::updateOrCreate(
            ['email' => 'admin@spindo.co.id'],
            [
                'name' => 'Admin WMS Spindo',
                'password' => Hash::make('spindo2026'),
            ]
        );

        // ─── Pipe Categories ───
        $catHitam = PipeCategory::updateOrCreate(['code' => 'PH'], ['name' => 'Pipa Hitam']);
        $catGalva = PipeCategory::updateOrCreate(['code' => 'PG'], ['name' => 'Pipa Galvanis']);

        // Data Dummy Pipa (Produk) sudah dihapus agar tidak tercampur dengan SIKUTA.

        // ─── 4 Physical Warehouses in one building ───
        // Each warehouse has 24 blocks (A1..A3 to L1..L3) with a central crane aisle.
        $warehouses = [
            ['code' => 'GUDANG-1', 'name' => 'Gudang 1 / Warehouse 1', 'category' => 'Area Pipa Hitam & Galvanis', 'total_capacity_tons' => 1200],
            ['code' => 'GUDANG-2', 'name' => 'Gudang 2 / Warehouse 2', 'category' => 'Area Pipa Galvanis & Medium', 'total_capacity_tons' => 1200],
            ['code' => 'GUDANG-3', 'name' => 'Gudang 3 / Warehouse 3', 'category' => 'Area Pipa Heavy & Special Spec', 'total_capacity_tons' => 1200],
            ['code' => 'GUDANG-4', 'name' => 'Gudang 4 / Warehouse 4', 'category' => 'Area Pipa Finish Good & Special Spec', 'total_capacity_tons' => 1200],
        ];

        // Specific block label mappings per Gudang based on physical floor plan signs
        $blockIdentities = [
            'GUDANG-3' => [
                'A1' => 'PIPA KOTAK GI',
                'A2' => 'EMPTY',
                'A3' => 'PIPA KOTAK GI',
                'B1' => 'PIPA KOTAK GI',
                'B2' => 'EMPTY',
                'B3' => 'PIPA KOTAK GI',
                'C1' => 'PIPA KOTAK GI',
                'C2' => 'PIPA KOTAK GI',
                'C3' => 'PIPA KOTAK GI',
                'D1' => 'PIPA KOTAK GI',
                'D2' => 'PIPA KOTAK GI',
                'D3' => 'PIPA KOTAK GI',
                'E1' => 'PIPA KOTAK GI',
                'E2' => 'PIPA KOTAK GI',
                'E3' => '8" SCH/PH',
                'F1' => 'EMPTY',
                'F2' => 'EMPTY',
                'F3' => '6" SCH/PH',
                'G1' => '4" MED/PH',
                'G2' => '6" MED/PAD',
                'G3' => '2" MED/PH',
                'H1' => '1" MED/PH',
                'H2' => '3" LGH/PAD',
                'H3' => '8" SCH/PH',
                'I1' => '3/4" SCH/PH',
                'I2' => 'PRE LOADING',
                'I3' => '1" MED/PAD',
                'J1' => '1-1/2" MED/PAD',
                'J2' => '8" MED/PAD',
                'J3' => '5" SCH/PH',
                'K1' => '3" MED/PH',
                'K2' => '4" MED/PH',
                'K3' => '1/2" MED/PAD',
                'L1' => 'EMPTY',
                'L2' => '8" MED/PH',
                'L3' => '8" LGH/PH',
            ],
            'GUDANG-2' => [
                'A1' => '1/2" MED/PAD',
                'A2' => '3/4" MED/PAD',
                'A3' => '1" MED/PAD',
                'B1' => '1-1/4" MED/PAD',
                'B2' => '1-1/2" MED/PAD',
                'B3' => '2" MED/PAD',
                'C1' => '2-1/2" MED/PAD',
                'C2' => '3" MED/PAD',
                'C3' => '4" MED/PAD',
                'D1' => 'PIPA KOTAK GI',
                'D2' => 'PIPA KOTAK GI',
                'D3' => 'PIPA KOTAK GI',
                'E1' => '5" SCH/PH',
                'E2' => '6" SCH/PH',
                'E3' => '8" SCH/PH',
                'F1' => '1" SCH/PH',
                'F2' => '2" SCH/PH',
                'F3' => '3" SCH/PH',
                'G1' => '2" MED/PH',
                'G2' => '3" MED/PH',
                'G3' => '4" MED/PH',
                'H1' => '6" MED/PH',
                'H2' => '8" MED/PH',
                'H3' => '10" SCH/PH',
                'I1' => '3/4" LGH/PAD',
                'I2' => 'PRE LOADING',
                'I3' => '1" LGH/PAD',
                'J1' => '1-1/2" LGH/PAD',
                'J2' => '2" LGH/PAD',
                'J3' => '3" LGH/PAD',
                'K1' => '4" LGH/PAD',
                'K2' => '6" LGH/PAD',
                'K3' => '8" LGH/PAD',
                'L1' => 'STOCK READY',
                'L2' => 'STOCK READY',
                'L3' => 'STOCK READY',
            ],
            'GUDANG-1' => [
                'A1' => 'SCH-40 1"',
                'A2' => 'SCH-40 1-1/4"',
                'A3' => 'SCH-40 1-1/2"',
                'B1' => 'SCH-40 2"',
                'B2' => 'SCH-40 2-1/2"',
                'B3' => 'SCH-40 3"',
                'C1' => 'SCH-40 4"',
                'C2' => 'SCH-40 5"',
                'C3' => 'SCH-40 6"',
                'D1' => 'MEDIUM 1"',
                'D2' => 'MEDIUM 1-1/4"',
                'D3' => 'MEDIUM 1-1/2"',
                'E1' => 'MEDIUM 2"',
                'E2' => 'MEDIUM 2-1/2"',
                'E3' => 'MEDIUM 3"',
                'F1' => 'MEDIUM 4"',
                'F2' => 'MEDIUM 6"',
                'F3' => 'MEDIUM 8"',
                'G1' => 'LGH/PH 1"',
                'G2' => 'LGH/PH 2"',
                'G3' => 'LGH/PH 3"',
                'H1' => 'LGH/PH 4"',
                'H2' => 'LGH/PH 6"',
                'H3' => 'LGH/PH 8"',
                'I1' => 'PRE LOADING A',
                'I2' => 'PRE LOADING B',
                'I3' => 'PRE LOADING C',
                'J1' => 'DISPATCH BAY 1',
                'J2' => 'DISPATCH BAY 2',
                'J3' => 'DISPATCH BAY 3',
                'K1' => 'QC HOLD BAY 1',
                'K2' => 'QC HOLD BAY 2',
                'K3' => 'QC HOLD BAY 3',
                'L1' => 'TEMPAT CADDY',
                'L2' => 'TANGGA MUAT',
                'L3' => 'AREA BUFFER',
            ],
        ];

        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        $rows = [1, 2, 3];

        foreach ($warehouses as $whData) {
            $zone = WarehouseZone::updateOrCreate(['code' => $whData['code']], $whData);
            $codePrefix = str_replace('GUDANG-', 'G', $whData['code']); // G1, G2, G3

            foreach ($columns as $col) {
                foreach ($rows as $row) {
                    $blockId = $col . $row; // A1, A2, A3 ... L3
                    $rackCode = $codePrefix . '-' . $blockId; // G1-A1, G2-B2, etc.
                    $assignedNote = $blockIdentities[$whData['code']][$blockId] ?? 'GENERIC PIPE BAY';

                    WarehouseRack::updateOrCreate(['rack_code' => $rackCode], [
                        'warehouse_zone_id' => $zone->id,
                        'rack_code' => $rackCode,
                        'block_code' => $blockId,
                        'sloc_code' => $this->slocCode($whData['code'], $blockId),
                        'area_code' => $this->areaCode($blockId),
                        'max_weight_tons' => 50.0, // Each block supports up to 50 Tons
                        'current_weight_tons' => 0.0,
                        'status' => 'AVAILABLE',
                    ]);
                }
            }
        }

        // Dummy Inventories dihapus.
    }

    private function areaCode(string $blockCode): string
    {
        $column = $blockCode[0];
        $row = (int) substr($blockCode, 1);

        return match (true) {
            $column <= 'D' => $row === 1 ? 'A1' : 'A2',
            $column <= 'H' => $row === 1 ? 'B1' : 'B2',
            default => $row === 1 ? 'C1' : 'C2',
        };
    }

    private function slocCode(string $warehouseCode, string $blockCode): string
    {
        $prefix = match ($warehouseCode) {
            'GUDANG-1' => '7A',
            'GUDANG-2' => '7B',
            'GUDANG-3' => '7C',
            'GUDANG-4' => '7D',
        };
        $column = $blockCode[0];
        $row = (int) substr($blockCode, 1);
        $group = $column <= 'D' ? 'A' : ($column <= 'H' ? 'B' : 'C');

        return $prefix . $group . ($row === 1 ? '1' : '2');
    }
}
