<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nombre', 'color', 'orden'])]
class EventoRapido extends Model
{
    protected $table = 'eventos_rapidos';
}
