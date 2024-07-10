<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

Route::get('/', function () {
    return view('chat');
});

Route::post('/send-message', [MessageController::class, 'sendMessage']);
Route::get('/get-messages', [MessageController::class, 'getMessages']);
