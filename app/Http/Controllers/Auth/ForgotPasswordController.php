<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\HasCountryBasedJobs;
use App\Models\Candidate;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use HasCountryBasedJobs, SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        if (! checkMailConfig()) {
            flashError(__('mail_not_sent_for_the_reason_of_incomplete_mail_configuration'));
        }

        $data['candidates'] = Candidate::count();

        return view('frontend.auth.passwords.email', $data);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * "Please wait before retrying." gives no idea how long the wait is, so a
     * throttled request now says how many seconds are left.
     *
     * @param  string  $response
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($response === Password::RESET_THROTTLED) {
            $response = trans('passwords.throttled_seconds', [
                'seconds' => $this->secondsUntilRetry($request->input('email')),
            ]);
        } else {
            $response = trans($response);
        }

        throw ValidationException::withMessages(['email' => [$response]]);
    }

    /**
     * Seconds left before another reset link may be requested.
     */
    protected function secondsUntilRetry(?string $email): int
    {
        $broker = config('auth.defaults.passwords');
        $throttle = (int) config("auth.passwords.{$broker}.throttle", 60);

        $record = DB::table(config("auth.passwords.{$broker}.table"))
            ->where('email', $email)
            ->first();

        if (! $record || ! $record->created_at) {
            return $throttle;
        }

        $elapsed = Carbon::parse($record->created_at)->diffInSeconds(Carbon::now());

        return (int) max(1, $throttle - $elapsed);
    }
}
