<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrintLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario',
        'documento',
        'data_impressao',
        'paginas',
        'custo',
        'aplicativo',
        'classificacao',
        'classificacao_origem',
    ];

    protected $casts = [
        'data_impressao' => 'datetime',
        'paginas' => 'integer',
        'custo' => 'decimal:4',
    ];

    public function overrides(): HasMany
    {
        return $this->hasMany(ManualOverride::class);
    }

    public function isPessoal(): bool
    {
        return $this->classificacao === 'PESSOAL';
    }

    public function isManual(): bool
    {
        return $this->classificacao_origem === 'MANUAL';
    }
}
