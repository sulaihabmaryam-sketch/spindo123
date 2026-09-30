<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipeProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'pipe_category_id',
        'sap_code',
        'nama_mudah',
        'description',
        'jenis',
        'nominal_size',
        'outer_diameter_mm',
        'spec_name',
        'wall_thickness_min',
        'wall_thickness_max',
        'is_threaded',
        'pcs_per_bundle',
        'weight_per_bundle_kg',
        'length_meters',
        'material_code',
    ];

    public function getNamaMudahAttribute($value): string
    {
        $isDrat = (bool) $this->is_threaded;
        if (!$isDrat && !empty($this->description)) {
            $descUpper = strtoupper($this->description);
            if (
                (str_contains($descUpper, 'THRD') || str_contains($descUpper, 'THREAD') || str_contains($descUpper, 'DRAT'))
                && !str_contains($descUpper, 'NON-DRAT') && !str_contains($descUpper, 'NON DRAT')
            ) {
                $isDrat = true;
            }
        }
        if (!$isDrat && preg_match('/^[GH][12]B10/i', (string) $this->sap_code)) {
            $isDrat = true;
        }

        if (!empty($value)) {
            $cleaned = trim(preg_replace('/\bNON[-\s]?DRAT\b/i', '', $value));
            $cleaned = preg_replace('/\s+/', ' ', $cleaned);
            if ($isDrat && !str_contains(strtoupper($cleaned), 'DRAT')) {
                $cleaned = preg_replace('/^(PIPA\s+(?:GALVA|HITAM|GALVANIS))/i', '$1 DRAT', $cleaned);
            }
            return $cleaned;
        }

        $jenis = $this->jenis ?: ($this->category?->code === 'PG' ? 'PIPA GALVA' : 'PIPA HITAM');
        $jenis = trim(preg_replace('/\bNON[-\s]?DRAT\b/i', '', $jenis));
        $jenis = preg_replace('/\s+/', ' ', $jenis);

        if ($isDrat && !str_contains(strtoupper($jenis), 'DRAT')) {
            $jenis .= ' DRAT';
        }

        $parts = array_filter([
            $jenis,
            $this->nominal_size,
            $this->spec_name,
            $this->sap_code,
        ]);
        return implode(' ', $parts);
    }

    public function getDescriptionAttribute($value): ?string
    {
        return !empty($value) ? $value : null;
    }

    protected $casts = [
        'is_threaded' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PipeCategory::class, 'pipe_category_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(PipeInventory::class);
    }
}
