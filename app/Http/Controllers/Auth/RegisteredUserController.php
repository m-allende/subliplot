<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = $this->validator($request,0);
        $error = $validator->errors();
        if ($error->first()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            $user->assignRole('buyer');

            Auth::login($user);

            return response()->json([
                'status' => 200,
                'errors' => $validator->messages(),
            ]);
        }
    }

    public function validator(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
        ];

        $messages =  [
            'name.required' => 'Debe ingresar Nombre',
            'email.required' => 'Debe ingresar Email',
            'password.required' => 'Debe ingresar Password',
            'name.string' => 'Debe ingresar Nombre Correcto',
            'name.max' => 'Debe ingresar Nombre Correcto',
            'email.string' => 'Debe ingresar Email Correcto',
            'email.email' => 'Debe ingresar Email Correcto',
            'email.unique' => 'Cuenta ya existe',
            'password.required' => 'Debe ingresar Contraseña',
            'password.confirmed' => 'Contraseña no coincide con la confirmación',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.mixed' => 'La contraseña debe incluir letras mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
            'password.symbols' => 'La contraseña debe incluir al menos un símbolo.',
            'password.uncompromised' => 'Esta contraseña ha sido comprometida en una filtración de datos. Elige otra.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }
}
