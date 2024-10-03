<?php
use Illuminate\Support\Facades\Route;
use Samurayjedi\LaravelDocusign\Controllers\JWTController;

Route::get('/docusign-auth', [
    JWTController::class,
    'handle',
  ])->name('docusign-oauth-jwt');
