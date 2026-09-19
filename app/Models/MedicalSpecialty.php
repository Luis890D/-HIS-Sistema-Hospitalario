<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalSpecialty extends Model
{
    protected $table = 'medical_specialties';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class, 'specialty_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'specialty_id');
    }
}
