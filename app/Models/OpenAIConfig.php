<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenAIConfig extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'openai_config';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'api_key',
        'created_by',
        'updated_by',
        'active',
    ];
    
    /**
     * Get the active API key or return null
     * 
     * @return string|null
     */
    public static function getActiveApiKey()
    {
        try {
            $config = self::where('active', true)->latest()->first();
            return $config ? $config->api_key : null;
        } catch (\Exception $e) {
            // If database is not available or table doesn't exist, return null
            return null;
        }
    }

    /**
     * Get the active API key with fallback to env variable
     * 
     * @return string|null
     */
    public static function getApiKey()
    {
        $apiKey = self::getActiveApiKey();
        return $apiKey ?? env('OPENAI_API_KEY');
    }
}
