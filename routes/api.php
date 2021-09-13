<?php

use Illuminate\Http\Request;
use App\Http\Controllers\WordQuestionController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HistoryController;
use App\Models\User;

// ユーザー
Route::post('/register', 'Auth\RegisterController@register')->name('register');
Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/user', function () { return Auth::user(); })->name('user'); // ログインユーザーの返却
Route::get('/users', function () { return User::get(); })->name('users'); // ログインユーザーの返却

// 問題
Route::put('/edit-word-question/{id}', [WordQuestionController::class, 'update'])->name('wordQuestion.update'); // 単語問題編集画面の表示
Route::get('/word-question-list', [WordQuestionController::class, 'readall'])->name('wordQuestion.readall'); // 単語問題一覧画面の表示
Route::get('/edit-word-question/{id}', [WordQuestionController::class, 'read'])->name('wordQuestion.read'); // 単語問題編集画面の表示
Route::get('/answer-word-question/{group}', [WordQuestionController::class, 'readgroup'])->name('wordQuestion.readgroup'); // 単語問題解答画面の表示
Route::post('/create-word-question/register', [WordQuestionController::class, 'create'])->name('wordQuestion.create'); // 単語問題登録

// グループ
Route::post('/create-group/register', [GroupController::class, 'create'])->name('group.create'); // グループ登録
Route::get('/create-group', [GroupController::class, 'readall'])->name('group.readall'); // グループの全取得

// 履歴
Route::post('/answer-word-question/register', [HistoryController::class, 'create'])->name('history.create'); // 履歴登録
Route::get('/admin', [HistoryController::class, 'readall'])->name('history.readall'); // 履歴の全取得
Route::get('/histories/{id}', [HistoryController::class, 'readuser'])->name('history.readuser'); // ユーザー履歴の取得