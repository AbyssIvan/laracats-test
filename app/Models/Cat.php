<?php

declare(strict_types=1);

namespace App\Models;

use App\Enum\Gender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property int $age
 * @property Gender $gender
 */
final class Cat extends Model
{
    protected $table = 'cats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'age',
        'gender',
        'mother_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
        ];
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(Cat::class, 'mother_id', 'id');
    }

    public function fathers() : HasMany
    {
        return $this->hasMany(CatFather::class, 'cat');
    }
}
