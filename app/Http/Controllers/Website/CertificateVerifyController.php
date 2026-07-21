<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateVerifyController extends Controller
{
    /**
     * Publicly verify a certificate by its unique UUID.
     */
    public function verify($uuid)
    {
        $certificate = Certificate::where('uuid', $uuid)
            ->orWhere('certificate_number', $uuid)
            ->first();

        if (!$certificate) {
            return view('frontend.pages.certificate-verify', [
                'error' => 'Certificate not found or invalid verification code.',
                'certificate' => null
            ]);
        }

        return view('frontend.pages.certificate-verify', compact('certificate'));
    }
}
