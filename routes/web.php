<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/api/documentation');
});

Route::get('/verified', function () {
    return view('verified');
});
Route::get('/reset-password/{token}', function ($token) {
    return redirect()->to(env("APP_CLIENT")."/reset/".$token);
})->name('password.reset');
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
->middleware(['signed', 'throttle:6,1'])
->name('verification.verify');

// Route::post('/register',RegisterController::class);

// if (App::environment(['production', 'staging'])) {
//     URL::forceScheme('https');
// }
