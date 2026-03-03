<?php

    namespace App\Http\Controllers;

    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\ValidationException;

    class AuthController extends Controller
    {
        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function register(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email:rfc,dns|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function login(Request $request)
        {
            $credentials = $request->validate([
                'email' => 'required|string|email:rfc,dns',
                'password' => 'required|string',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }

            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function logout(Request $request)
        {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login.form');
        }
    }