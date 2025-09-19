<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'assessment_id', 
        'interest_url', 
        'individual_url', 
        'openia', 
        'resumen_openia',
        'is_processing'
    ];
}
