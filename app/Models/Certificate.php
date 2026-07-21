<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'certificate_number',
        'user_id',
        'first_name',
        'last_name',
        'course_name',
        'duration',
        'certificate_date',
        'status',
        'issued_by',
    ];

    protected $casts = [
        'certificate_date' => 'date',
    ];

    /**
     * Boot the model – auto-generate short UUID code and certificate number on creation.
     */
    protected static function booted()
    {
        static::creating(function ($certificate) {
            if (empty($certificate->uuid)) {
                $certificate->uuid = strtolower(Str::random(8));
            }

            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = static::generateCertificateNumber();
            }
        });
    }

    /**
     * Generate a unique certificate number in format: A{5-digit-serial}
     * Example: A00001 (Total 6 characters max)
     */
    public static function generateCertificateNumber(): string
    {
        $prefix = 'A';

        $lastCert = static::where('certificate_number', 'like', 'A%')
            ->orderByRaw('LENGTH(certificate_number) DESC, certificate_number DESC')
            ->first();

        if ($lastCert) {
            $lastSerial = (int) substr($lastCert->certificate_number, 1);
            $nextSerial = $lastSerial + 1;
        } else {
            $nextSerial = 1;
        }

        return $prefix . str_pad($nextSerial, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the public verification URL for this certificate.
     */
    public function getVerifyUrlAttribute(): string
    {
        return url('/certificate/verify/' . $this->uuid);
    }

    /**
     * Relationship: certificate belongs to a user (candidate).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: certificate was issued by an admin.
     */
    public function issuer()
    {
        return $this->belongsTo(Admin::class, 'issued_by');
    }
}
