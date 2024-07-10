<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model 
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'address',
        'postcode',
        'city',
        'stateAbbr',
        'country'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
