<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [NoteController::class, 'index'])->name('notes.index');
Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
Route::patch('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
Route::post('/notes/{note}/pin', [NoteController::class, 'togglePin'])->name('notes.pin');
Route::post('/notes/import', [NoteController::class, 'import'])->name('notes.import');
Route::get('/notes/{note}/export', [NoteController::class, 'export'])->name('notes.export');
