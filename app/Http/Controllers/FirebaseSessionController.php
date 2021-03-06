<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Providers\RouteServiceProvider;
use App\Facades\Helpers\FirebaseHelper as Firebase;

class FirebaseSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function createSession()
    {
        request()->validate([
            'token' => ['required'],
        ]);
        try {
            $user = Firebase::user();
            if ($user == null) {
                Firebase::createSessionCookie(request()->post('token'));
                return response()->json([
                    'message' => __('Successfully logged-in'),
                    'redirect' => route('firebase'),
                ], 200);
            } else {
                $now = new \DateTimeImmutable(gmdate('D, d M Y H:i:s T', time()));
                if ($user['exp']->diff($now)->days <= 7) {
                    Firebase::createSessionCookie(request()->post('token'));
                    return response()->json(['message' => __('Successfully refresh the token')], 200);
                }
                return response()->json(['message' => __('Already logged-in')], 200);
            }
        } catch (\Exception $e) {
            // @TODO proper catch \Kreait\Firebase\Exception\Auth
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroySession()
    {
        Firebase::destroySessionCookie();
        return redirect('/');
    }
}
