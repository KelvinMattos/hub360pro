<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $table = 'integrations';

    protected $fillable = [
        'company_id',
        'platform',
        'client_id',
        'client_secret',
        'commission_percent',
        'fixed_fee',
        'participates_in_program',
        'access_token',
        'refresh_token',
        'expires_at',
        'auto_fetch_fees'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'participates_in_program' => 'boolean',
        'auto_fetch_fees' => 'boolean',
        'commission_percent' => 'float',
        'fixed_fee' => 'float'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}