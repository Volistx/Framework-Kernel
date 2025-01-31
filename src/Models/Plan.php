<?php

namespace Volistx\FrameworkKernel\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Symfony\Component\Uid\Ulid;

class Plan extends Model
{
    use HasFactory;
    use HasUlids;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'tag',
        'description',
        'is_active',
        'data',
        'price',
        'custom',
        'tier',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'data' => 'array',
        'price' => 'float',
        'custom' => 'boolean',
        'tier' => 'integer',
        'created_at' => 'date:Y-m-d H:i:s',
        'updated_at' => 'date:Y-m-d H:i:s',
    ];

    public function subscriptions(): HasMany
    {
        return $this->HasMany(Subscription::class);
    }

    public function newUniqueId()
    {
        return Str::ulid()->toRfc4122();
    }

    protected function getUlidAttribute()
    {
        return Ulid::fromString($this->attributes['id']);
    }
}
