<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return view('auth');
    }

    return view('guest');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::post('/signup', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:50', 'min:3'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed'],
        ]);
    
        $user = User::create($request->toArray());
    
        auth()->login($user);
    
        return $user;
    })->name('signup');
    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($request->toArray())) {
            return response()->json(['message'=> 'Invaild Credentials!'], 422);
        }
    
        return auth()->user();
    })->name('login');
});

Route::middleware('auth')->get('logout', function () {
    auth()->logout();
    return redirect()->back();
})->name('logout');