<?php
//
//The model.
use App\Models\Word;
//
//The controller.
use App\Http\Controllers\TranslateController;
use App\Http\Controllers\Add_wordController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\PublicController;
//
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|---------------------------------- ----------------------------------------
| KENTIONARY ROUTES
|--------------------------------------------------------------------------
*/
/**
 * 
 * KENTIONARY
 * 
 * ===== TRANSLATE =====
 */
//
//Translate a word using the language provided.
Route::post('/translate/get_translation', [TranslateController::class, 'get_translation']);
//
//Translate a word from one language to another language specified by the user.
Route::post('/translate/get_translation_filter', [TranslateController::class, 'get_translation_filter']);
//
//Get the list of languages available in the database.
Route::get('/translate/get_language', [TranslateController::class, 'get_language']);
//
//Get the list of languages available in the database.
Route::post('/translate/auto_suggest', [TranslateController::class, 'auto_suggest']);
/**
 * ===== SYNONYM PROVIDED =====
 * 
 * ===== CHECK IF A WORD(S) EXISTS IN THE DATABASE. =====
 * ===== AT THIS POINT THE USER HAS PROVIDED A SYNONYM. =====
*/
Route::post('/check/get_term', [CheckController::class, 'get_term']);
Route::post('/check/get_request', [CheckController::class, 'get_request']);
/**
 * ===== ADD A NEW WORD =====
 * 
 * Get the list of categories.
*/
Route::get('/add_word/get_category', [Add_wordController::class, 'get_category']);
Route::post('/add_word/get_request', [Add_wordController::class, 'get_request']);
/**
 * ===== YES TERM, NO SYNONYM =====
 * 
 * Check if the words (without the synonym, term is provided) to be added exist in the database.
 */
Route::post('/check/get_request_no_synonym', [CheckController::class, 'get_request_no_synonym']);
/**
 * ===== NEW TERM, NO SYNONYM ===== 
 * 
 * Check if the words (without the synonym, new term provided) to be added exist in the database.
 */
Route::post('/check/get_request_term_no_synonym', [CheckController::class, 'get_request_term_no_synonym']);
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
/**
 * Check if a word exists in the database.
 */
Route::post('/public/checker', [PublicController::class, 'checker']);
/**
 * Translate a word for the public.
 */
Route::post('/public/get_translation', [PublicController::class, 'get_translation']);
/**
 * Add a new word through the public developers.
 */
Route::post('/public/add_word', [PublicController::class, 'add_word']);
/**
 * ===== TRANSLATE MANY WORDS, PROVIDED IN AN ARRAY =====
 */
Route::post('/translate/translate_collection', [TranslateController::class, 'translate_collection']);