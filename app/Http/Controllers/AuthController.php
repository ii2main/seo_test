<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\User\UserCreateRequest;
    use App\Http\Requests\User\UserLoginRequest;
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
        public function register(UserCreateRequest $request)
        {
            $data = $request->validated();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('ranks.index'));
        }

        /**
         * @param Request $request
         * @return \Illuminate\Http\RedirectResponse
         */
        public function login(UserLoginRequest $request)
        {
            $credentials = $request->validated();

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->intended(route('ranks.index'));
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