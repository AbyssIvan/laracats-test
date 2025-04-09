<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property int $cat
 * @property int $father
 */
class CatFather extends Model
{
    protected $table = 'cat_fathers';
    
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cat',
        'father',
    ];

    public function cat() : BelongsTo
    {
        return $this->belongsTo(Cat::class);
    }

    public function father() : BelongsTo
    {
        return $this->belongsTo(Cat::class);
    }
}
