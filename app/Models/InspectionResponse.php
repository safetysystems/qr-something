<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'inspection_template_item_id',
        'response',
        'notes',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function templateItem(): BelongsTo
    {
        return $this->belongsTo(InspectionTemplateItem::class, 'inspection_template_item_id');
    }
}
