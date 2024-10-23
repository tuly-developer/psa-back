<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    public function __invoke(Request $request): UserResource|JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) return response()->json(['message' => 'Email not found'], Response::HTTP_UNAUTHORIZED);

        if (!auth()->attempt($credentials)) return response()->json(['message' => 'Invalid user data'], Response::HTTP_UNAUTHORIZED);

        $user = auth()->user();

        $token = $user->createToken($user->email . ' - ' . time());

        $user->token = $token->plainTextToken;

        return new UserResource($user);
    }

    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            $status = Password::sendResetLink($request->only('email'));

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => 'Reset link sent to your email.', 'status' => $status])
                : response()->json(['message' => 'Unable to send reset link', 'status' => $status], Response::HTTP_BAD_REQUEST);
        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            return response()->json([
                'message' => 'Failed to send reset link. Please verify your email address.',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function reset(Request $request): View|Factory|Application
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return view('auth.passwords.success');
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }

    public function impersonate(User $user): JsonResponse
    {
        if (!auth()->user()->admin) return response()->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        if (!$user) return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);

        $impersonatorEmail = auth()->user()->email;
        $targetUserEmail = $user->email;
        $tokenName = "{$impersonatorEmail} is impersonating {$targetUserEmail}";

        $token = $user->createToken($tokenName, ['*'], Carbon::now()->addMinutes(30))->plainTextToken;

        return response()->json([
            'message' => 'Successfully impersonated user',
            'token' => $token
        ], Response::HTTP_OK);
    }
}
