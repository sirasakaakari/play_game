<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\Over50Controller;
use Illuminate\Support\Facades\Route;

/* ゲーム画面 */
Route::get('/battle', [CardController::class, 'play'])->name('battle');

/* ルート / はそのままゲームへ */
Route::get('/', fn () => redirect()->route('battle'));

Route::get('/over50', [Over50Controller::class, 'show'])->name('over50.show');
Route::post('/over50/draw', [Over50Controller::class, 'draw'])->name('over50.draw');
Route::post('/over50/restart', [Over50Controller::class, 'restart'])->name('over50.restart');
Route::post('/over50/minus', [Over50Controller::class, 'minus'])->name('over50.minus');
