<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User as SocialiteUser;
use Auth;
use Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    public function loginwith()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->email)->first();
            if($user){
                Auth::login($user);
                return redirect('dashboard');

            }else{
                $newUser = User::create([
                    'name' => ucwords($googleUser->name),
                    'email' => $googleUser->email,
                    'email_verfied_at' => now(),
                    'password' => Hash::make(123456),
                    'remember_token' => Str::random(10),
                ]);
                Auth::login($newUser);
                return redirect('dashboard');
            }
        } catch (\Throwable $th) {
            abort(404);
        }
    }
    
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function redirectToAuth(): JsonResponse
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }

    public function handleAuthCallback(): JsonResponse
    {
        try {
            /** @var SocialiteUser $socialiteUser */
            $socialiteUser = Socialite::driver('google')->stateless()->user();
        } catch (ClientException $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        /** @var User $user */
        $user = User::query()
            ->firstOrCreate(
                [
                    'email' => $socialiteUser->getEmail(),
                ],
                [
                    'email_verified_at' => now(),
                    'name' => $socialiteUser->getName(),
                    'google_id' => $socialiteUser->getId(),
                    'avatar' => $socialiteUser->getAvatar(),
                ]
            );

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken('google-token')->plainTextToken,
            'token_type' => 'Bearer',
        ]);
    }


}
