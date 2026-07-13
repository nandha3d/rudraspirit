<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\MailManager;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Rules\Recaptcha;
use Illuminate\Validation\Rule;
use App\Utility\SmsUtility;
use Mail;

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

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    /**
     * Show the reset-code form at its own GET url (email carried in the query
     * so it survives a failed reset submit). Prevents the 405 from a stray
     * GET on the POST-only /password/email route.
     */
    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        return view('auth.' . get_setting('authentication_layout_select') . '.reset_password', compact('email'));
    }

    public function sendResetLinkEmail(Request $request)
    {

        // validate recaptcha
        $request->validate([
            'g-recaptcha-response' => [
                Rule::when(get_setting('google_recaptcha') == 1 && get_setting('recaptcha_forgot_password') == 1, ['required', new Recaptcha()], ['sometimes'])
            ],
        ]);
        
        $phone = "+{$request['country_code']}{$request['phone']}";
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->email)->first();
            if ($user != null) {
                $user->verification_code = rand(100000,999999);
                $user->save();
                
                // Guard against a missing template row (e.g. email_templates not seeded)
                // so the reset flow falls back to a default mail instead of a 500.
                $emailTemplate = EmailTemplate::whereIdentifier('password_reset_email_to_all')->first();
                $emailSubject = $emailTemplate?->subject ?? '[[store_name]] - Password Reset';
                $emailSubject = str_replace('[[store_name]]', get_setting('site_name'), $emailSubject);

                $email_body = $emailTemplate?->default_text ?? 'Hello [[user_email]],<br><br>Your password reset code is <strong>[[code]]</strong>.<br><br>Regards,<br>[[store_name]]';
                $email_body = str_replace('[[user_email]]', $user->email, $email_body);
                $email_body = str_replace('[[code]]', $user->verification_code, $email_body);
                $email_body = str_replace('[[store_name]]', get_setting('site_name'), $email_body);
                
                $array['subject'] = $emailSubject;
                $array['content'] = $email_body;
                Mail::to($user->email)->queue(new MailManager($array));

                // Post/Redirect/Get: send the user to a GET url for the reset form
                // so refresh / back / a failed reset submit never hit POST-only
                // /password/email (which would 405).
                return redirect()->route('password.reset_form', ['email' => $user->email]);
            }
            else {
                flash(translate('No account exists with this email'))->error();
                return back();
            }
        }
        else{
            $user = User::where('phone', $phone)->first();
            if ($user != null) {
                $user->verification_code = rand(100000,999999);
                $user->save();
                SmsUtility::password_reset($user);
                $country_code= $request['country_code'];
                return view('otp_systems.frontend.auth.'.get_setting('authentication_layout_select').'.reset_with_phone', compact('phone','country_code'));
            }
            else {
                flash(translate('No account exists with this phone number'))->error();
                return back();
            }
        }
    }
}
