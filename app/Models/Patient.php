<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $table = 'patients';

    protected $fillable = [
        'medical_record_number', // Número de expediente / historia clínica
        'identification_number', // DPI o Cédula
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'blood_type',
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'allergies',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
}
