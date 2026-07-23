<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Courses;
use App\Models\User;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of the certificates.
     */
    public function index(Request $request)
    {
        $query = Certificate::with('user')->latest();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('certificate_number', 'like', "%{$keyword}%")
                  ->orWhere('first_name', 'like', "%{$keyword}%")
                  ->orWhere('last_name', 'like', "%{$keyword}%")
                  ->orWhere('course_name', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->paginate(20);

        return view('backend.certificates.index', compact('certificates'));
    }

    /**
     * Show the form for creating a new certificate.
     */
    public function create()
    {
        // Fetch all active courses
        $courses = Courses::where('is_active', 1)->orderBy('title')->get();

        return view('backend.certificates.create', compact('courses'));
    }

    /**
     * Store a newly created certificate in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
            'duration' => 'required|string|max:255',
            'certificate_date' => 'required|date',
        ]);

        $certificate = Certificate::create([
            'user_id' => $request->user_id ?: null,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'course_name' => $request->course_name,
            'duration' => $request->duration,
            'certificate_date' => $request->certificate_date,
            'status' => 'issued',
            'issued_by' => auth('admin')->id(),
        ]);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate generated successfully.');
    }

    /**
     * AJAX: Lookup a candidate by email and return their name + id.
     */
    public function lookupByEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['found' => false]);
        }

        $parts = explode(' ', $user->name, 2);

        return response()->json([
            'found' => true,
            'user_id' => $user->id,
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ]);
    }

    /**
     * Display the specified certificate.
     */
    public function show(Certificate $certificate)
    {
        return view('backend.certificates.show', compact('certificate'));
    }

    /**
     * Dedicated standalone print view for certificate.
     */
    public function print(Certificate $certificate)
    {
        return view('backend.certificates.print', compact('certificate'));
    }

    /**
     * Update the status of certificate to sent.
     */
    public function sendToUser(Certificate $certificate)
    {
        $certificate->update(['status' => 'sent']);

        return back()->with('success', 'Certificate status updated to Sent.');
    }

    /**
     * Remove the specified certificate from storage.
     */
    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }
}
