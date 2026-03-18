<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'uuid',
        'workplace_id',
        'name',
        'type',
        'asset_code',
        'serial_number',
        'manufacturer',
        'model',
        'location_label',
        'notes',
        'qr_code_path',
        'qr_code_generated_at',
        'archived_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
            'qr_code_generated_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function workplace(): BelongsTo
    {
        return $this->belongsTo(Workplace::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }
}
