<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'print_log_id',
        'classificacao_anterior',
        'classificacao_nova',
        'alterado_por',
    ];

    public function printLog(): BelongsTo
    {
        return $this->belongsTo(PrintLog::class);
    }
}
