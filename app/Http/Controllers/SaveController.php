<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class SaveController extends Controller
{
    /*
    * ===== SYNONYMS PROVIDED AT THIS POINT. =====
    */
    //
    //Save the word to translate to and the synonym.
    static function save_translation_to_synonym($translation_to, $translation_from, $synonym, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        $word_synonym = $synonym['word']; 
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        $word_synonym_id = @DataController::get_word_id($word_synonym)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        $language_synonym = $synonym['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        $language_synonym_id = DataController::get_language_id($language_synonym)['data'];
        //
        //The term to link with a word.
        $term_from = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_from)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        $meaning_synonym = $synonym['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        $example_synonym = $synonym['sentence'];
        //
        //Check if the word already exists in the database.
        $state_to = CheckController::check_if_word_exists($word_to);
        $state_synonym = CheckController::check_if_word_exists($word_synonym);
        //
        //After checking if word exists in the word table, proceed accordingly.
        //
        //In this case, both the word to translate to and the synonym exist in the database.
        if ($state_to === true && $state_synonym === true) {
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Only the word to translate to exists in the database.
        elseif($state_to === true && $state_synonym !== true){
            //
            //1. Create the required translation and synonym (for the word to translate to).
            CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Only the synonym exists in the database.
        elseif($state_to !== true && $state_synonym === true){
            //
            //1. Create the required translation and synonym (for the synonym).
            CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Neither the word to translate to and the synonym exist in the database.
        elseif($state_to !== true && $state_synonym !== true){
            //
            // Create the word to translate to, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_to_id, $word_to
            );
            //
            // Create the word's synonym, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_synonym_id, $word_synonym
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example(
                $term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
    }
    //
    //Save the synonym.
    static function save_translation_from_synonym($translation_to, $translation_from, $synonym, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        $word_synonym = $synonym['word']; 
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        $word_synonym_id = @DataController::get_word_id($word_synonym)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        $language_synonym = $synonym['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        $language_synonym_id = DataController::get_language_id($language_synonym)['data'];
        //
        //The term to link with a word.
        $term_from = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_from)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        $meaning_synonym = $synonym['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        $example_synonym = $synonym['sentence'];
        //
        //Check if the word already exists in the database.
        $state_from = CheckController::check_if_word_exists($word_from);
        $state_synonym = CheckController::check_if_word_exists($word_synonym);
        //
        //After checking if word exists in the word table, proceed accordingly.
        //
        //In this case, both the word to translate from and the synonym exist in the database.
        if ($state_from === true && $state_synonym === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Only the word to translate from exists in the database.
        elseif($state_from === true && $state_synonym !== true){
            //
            //1. Create the required translation and synonym (for the word to translate to).
            CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Only the synonym exists in the database.
        elseif($state_from !== true && $state_synonym === true){
            //
            //1. Create the required translation and synonym (for the synonym).
            CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Neither the word to translate to and the synonym exist in the database.
        elseif($state_from !== true && $state_synonym !== true){
            //
            // Create the word to translate from, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_from_id, $word_from
            );
            //
            // Create the word's synonym, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_synonym_id, $word_synonym
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example(
                $term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
    }
    //
    //Save the word to translate from and to.
    static function save_translation_from_to($translation_to, $translation_from, $synonym, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        @$word_synonym = $synonym['word']; 
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        $word_synonym_id = @DataController::get_word_id($word_synonym)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        @$language_synonym = $synonym['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        @$language_synonym_id = DataController::get_language_id($language_synonym)['data'];
        //
        //The term to link with a word.
        $term_from = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_from)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        @$meaning_synonym = $synonym['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        @$example_synonym = $synonym['sentence'];
        //
        //Check if the word already exists in the database.
        $state_from = CheckController::check_if_word_exists($word_from);
        $state_to = CheckController::check_if_word_exists($word_to);
        //
        //After checking if word exists in the word table, proceed accordingly.
        //
        //In this case, both the word to translate from and the synonym exist in the database.
        if ($state_from === true && $state_to === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Only the word to translate from exists in the database.
        elseif($state_from === true && $state_to !== true){
            //
            //1. Create the required translation and synonym (for the word to translate to).
            CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Only the word to translate to exists in the database.
        elseif($state_from !== true && $state_to === true){
            //
            //1. Create the required translation and synonym (for the synonym).
            CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Neither the word to translate to and the synonym exist in the database.
        elseif($state_from !== true && $state_to !== true){
            //
            // Create the word to translate from, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_from_id, $word_from
            );
            //
            // Create the word to translate to, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_to_id, $word_to
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example(
                $term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
    }
    //
    //Save the synonym.
    static function save_synonym($translation_from, $translation_to, $synonym, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        $word_synonym = $synonym['word']; 
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        $word_synonym_id = @DataController::get_word_id($word_synonym)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        $language_synonym = $synonym['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        $language_synonym_id = DataController::get_language_id($language_synonym)['data'];
        //
        //The term to link with a word.
        $term_from = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_from)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        $meaning_synonym = $synonym['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        $example_synonym = $synonym['sentence'];
        //
        //Check if the word already exists in the database.
        $state_synonym = CheckController::check_if_word_exists($word_synonym);
        //
        //After checking if word exists in the word table, proceed accordingly.
        if ($state_synonym === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
        else{
            //
            // Create the word, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_synonym_id, $word_synonym
            );
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
    }
    //
    //Save the word to translate to.
    static function save_translation_to($translation_to, $translation_from, $synonym, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        $word_synonym = $synonym['word']; 
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        $word_synonym_id = @DataController::get_word_id($word_synonym)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        $language_synonym = $synonym['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        $language_synonym_id = DataController::get_language_id($language_synonym)['data'];
        //
        //The term to link with a word.
        $term_to = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_to)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        $meaning_synonym = $synonym['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        $example_synonym = $synonym['sentence'];
        //
        //Check if the word already exists in the database.
        $state_to = CheckController::check_if_word_exists($word_to);
        //
        //After checking if word exists in the word table, save the word and the synonym.
        if ($state_to === true) {
            ///
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        //
        //Neither the word nor its synonym exist in the database.
        else{
            //
            // Create the word, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_to_id, $word_to
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example(
                $term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
    }
    //
    //Save the word to translate from.
    static function save_translation_from($translation_from, $translation_to, $synonym, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        @$word_synonym = $synonym['word']; 
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        @$word_synonym_id = @DataController::get_word_id($word_synonym)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        @$language_synonym = $synonym['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        @$language_synonym_id = DataController::get_language_id($language_synonym)['data'];
        //
        //The term to link with a word.
        $term_from = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_from)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        @$meaning_synonym = $synonym['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        @$example_synonym = $synonym['sentence'];
        //
        //Check if the word already exists in the database.
        $state_from = CheckController::check_if_word_exists($word_from);
        //
        //After checking if word exists in the word table, proceed accordingly.
        if ($state_from === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
        else{
            //
            // Create the word, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_from_id, $word_from
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            //
            //Add the meaning and example for the synonym.
            CreateController::create_meaning_example(
                $term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
        }
    }
    //
    //Save the word to translate from, translation to and the synonym.
    static function save_all($translation_to, $translation_from, $synonym, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        $word_synonym = $synonym['word'];
        //
        //The term to link with a word.
        $term_name = $term['term'];
        $term_type = $term['category'];
        //
        //The term's id.
        // $term_id = DataController::get_term_id($term_name)['data'];
        //
        //Check if the word already exists in the database.
        $state_from = CheckController::check_if_word_exists($word_from);
        $state_to = CheckController::check_if_word_exists($word_to);
        $state_synonym = CheckController::check_if_word_exists($word_synonym);
        $state_term = CheckController::check_if_term_exists($term_name, $term_type);
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        $word_synonym_id = @DataController::get_word_id($word_synonym)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        $language_synonym = $synonym['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        $language_synonym_id = DataController::get_language_id($language_synonym)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        $meaning_synonym = $synonym['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        $example_synonym = $synonym['sentence'];
        //
        //First check if the term exists in the database.
        //
        //At this point the term is in the database.
        if ($state_term === true) {
            //
            //Get term's id.
            $term_id = DataController::get_term_id($term_from)['data'];
            //
            //After checking if word exists in the word table, proceed accordingly.
            //
            //In this case, the word to translate from, translate to and the synonym exist in the database.
            if ($state_from === true && $state_to === true && $state_synonym === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Only the word from translate from exists in the database.
            elseif($state_from === true && $state_to !== true && $state_synonym !== true){
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Only the word to translate to exists in the database.
            elseif($state_from !== true && $state_to === true && $state_synonym !== true){
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Only the synonym exists in the database.
            elseif($state_from !== true && $state_to !== true && $state_synonym === true){
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Neither the word to translate from, the word to translate to nor the synonym exist in the database.
            elseif($state_from !== true && $state_to !== true && $state_synonym !== true){
                //
                // Create the word to translate from, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_from_id, $word_from, $meaning_from, $example_from
                );
                //
                // Create the word to translate to, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_to_id, $word_to, $meaning_to, $example_to
                );
                //
                // Create the synonym, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_synonym_id, $word_synonym, $meaning_synonym, $example_synonym
                );
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example(
                    $term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
        }
        //
        //At this point the term is not in the database.
        else{
            //
            //Add the new term in the database and retrieve its primary key.
            $term_id = CreateController::create_term($term_name, $term_type);
            //
            //Create the required term.
            CreateController::create_word_translation_synonym($term_id, '15', $term_name);
            //
            //After checking if word exists in the word table, proceed accordingly.
            //
            //In this case, the word to translate from, translate to and the synonym exist in the database.
            if ($state_from === true && $state_to === true && $state_synonym === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //2. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //3. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Only the word from translate from exists in the database.
            elseif($state_from === true && $state_to !== true && $state_synonym !== true){
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Only the word to translate to exists in the database.
            elseif($state_from !== true && $state_to === true && $state_synonym !== true){
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Only the synonym exists in the database.
            elseif($state_from !== true && $state_to !== true && $state_synonym === true){
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_synonym_id, $word_synonym_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example($term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
            //
            //Neither the word to translate from, the word to translate to nor the synonym exist in the database.
            elseif($state_from !== true && $state_to !== true && $state_synonym !== true){
                //
                // Create the word to translate from, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_from_id, $word_from, $meaning_from, $example_from
                );
                //
                // Create the word to translate to, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_to_id, $word_to, $meaning_to, $example_to
                );
                //
                // Create the synonym, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_synonym_id, $word_synonym, $meaning_synonym, $example_synonym
                );
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the synonym.
                CreateController::create_meaning_example(
                    $term_id, $word_synonym, $language_synonym_id, $meaning_synonym, $example_synonym);
            }
        }
    }
    /*
    * ===== NO SYNONYMS PROVIDED AT THIS POINT. =====
    */
    //
    //Save the word to translate from.
    static function save_translation_from_no_synonym($translation_from, $translation_to, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        //
        //The term to link with a word.
        $term_from = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_from)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        //
        //Check if the word already exists in the database.
        $state_from = CheckController::check_if_word_exists($word_from);
        //
        //After checking if word exists in the word table, proceed accordingly.
        if ($state_from === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
        else{
            //
            // Create the word, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_from_id, $word_from
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
    }
    //
    //Save the word to translate to.
    static function save_translation_to_no_synonym($translation_to, $translation_from, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        //
        //The term to link with a word.
        $term_to = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_to)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        //
        //Check if the word already exists in the database.
        $state_to = CheckController::check_if_word_exists($word_to);
        //
        //After checking if word exists in the word table, save the word and the synonym.
        if ($state_to === true) {
            ///
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
        }
        //
        //Neither the word nor its synonym exist in the database.
        else{
            //
            // Create the word, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_to_id, $word_to
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
    }
    //
    //Save the word to translate from and to.
    static function save_translation_from_to_no_synonym($translation_to, $translation_from, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        //
        //The term to link with a word.
        $term_from = $term['term'];
        //
        //The term's id.
        $term_id = DataController::get_term_id($term_from)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        //
        //Check if the word already exists in the database.
        $state_from = CheckController::check_if_word_exists($word_from);
        $state_to = CheckController::check_if_word_exists($word_to);
        //
        //After checking if word exists in the word table, proceed accordingly.
        //
        //In this case, both the word to translate from and the synonym exist in the database.
        if ($state_from === true && $state_to === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
        //
        //Only the word to translate from exists in the database.
        elseif($state_from === true && $state_to !== true){
            //
            //1. Create the required translation and synonym (for the word to translate to).
            CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
        //
        //Only the word to translate to exists in the database.
        elseif($state_from !== true && $state_to === true){
            //
            //1. Create the required translation and synonym (for the synonym).
            CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
        //
        //Neither the word to translate to and the synonym exist in the database.
        elseif($state_from !== true && $state_to !== true){
            //
            // Create the word to translate from, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_from_id, $word_from
            );
            //
            // Create the word to translate to, its synonym and translation. This will link them to the 
            //right term.
            CreateController::create_word_translation_synonym(
                $term_id, $language_to_id, $word_to
            );
            //
            //Add the meaning and example for the word to translate from.
            CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
            //
            //Add the meaning and example for the word to translate to.
            CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
        }
    }
    /**
     * ===== NEW TERM, NO SYNONYMS =====
     */
    //
    //Save the word to translate from and to.
    static function save_new_term_translation_from_to_no_synonym($translation_to, $translation_from, $term){
        //
        //The words to add in the database.
        $word_from = $translation_from['word'];
        $word_to = $translation_to['word'];
        //
        //The term to link with a word.
        $term_name = $term['term'];
        $term_type = $term['category'];
        //
        //Check if the word already exists in the database.
        $state_from = CheckController::check_if_word_exists($word_from);
        $state_to = CheckController::check_if_word_exists($word_to);
        $state_term = CheckController::check_if_term_exists($term_name, $term_type);
        //
        //The words' ids.
        $word_from_id = @DataController::get_word_id($word_from)['data'];
        $word_to_id = @DataController::get_word_id($word_to)['data'];
        //
        //The words' language.
        $language_from = $translation_from['language'];
        $language_to = $translation_to['language'];
        //
        //The languages' id.
        $language_from_id = DataController::get_language_id($language_from)['data'];
        $language_to_id = DataController::get_language_id($language_to)['data'];
        //
        //The meanings of the word.
        $meaning_from = $translation_from['meaning'];
        $meaning_to = $translation_to['meaning'];
        //
        //The example sentences.
        $example_from = $translation_from['sentence'];
        $example_to = $translation_to['sentence'];
        //
        //First check if the term exists in the database.
        //
        //At this point the term is in the database.
        if ($state_term === true) {
            //
            //Get term's id.
            $term_id = DataController::get_term_id($term_from)['data'];
            //
            //After checking if word exists in the word table, proceed accordingly.
            //
            //In this case, the word to translate from and translate to exist in the database.
            if ($state_from === true && $state_to === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
            //
            //Only the word from translate from exists in the database.
            elseif($state_from === true && $state_to !== true){
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
            //
            //Only the word to translate to exists in the database.
            elseif($state_from !== true && $state_to === true){
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
            //
            //Neither the word to translate from nor the word to translate to exists in the database.
            elseif($state_from !== true && $state_to !== true){
                //
                // Create the word to translate from, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_from_id, $word_from, $meaning_from, $example_from
                );
                //
                // Create the word to translate to, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_to_id, $word_to, $meaning_to, $example_to
                );
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
        }
        //
        //At this point the term is not in the database.
        else{
            //
            //Add the new term in the database and retrieve its primary key.
            $term_id = CreateController::create_term($term_name, $term_type);
            //
            //Create the required term, its translation and synonym.
            //
            //The term needs to be an English word.
            CreateController::create_word_translation_synonym($term_id, '15', $term_name);
            //
            //After checking if word exists in the word table, proceed accordingly.
            //
            //In this case, the word to translate from and translate to exist in the database.
            if ($state_from === true && $state_to === true) {
                //
                //1. Create the required translation and synonym (for the word to translate from).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
            //
            //Only the word to translate from exists in the database.
            elseif($state_from === true && $state_to !== true){
                //
                //1. Create the required translation and synonym (for the word to translate to).
                CreateController::create_translation_and_synonym($term_id, $language_from_id, $word_from_id);
                //
                // Create the word to translate tp, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_to_id, $word_to, $meaning_to, $example_to
                );
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
            //
            //Only the word to translate to exists in the database.
            elseif($state_from !== true && $state_to === true){
                //
                //1. Create the required translation and synonym (for the synonym).
                CreateController::create_translation_and_synonym($term_id, $language_to_id, $word_to_id);
                //
                // Create the word to translate from, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_from_id, $word_from, $meaning_from, $example_from
                );
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
            //
            //Neither the word to translate from nor the word to translate to exists in the database.
            elseif($state_from !== true && $state_to !== true){
                //
                // Create the word to translate from, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_from_id, $word_from, $meaning_from, $example_from
                );
                //
                // Create the word to translate to, its synonym and translation. This will link them to the 
                //right term.
                CreateController::create_word_translation_synonym(
                    $term_id, $language_to_id, $word_to, $meaning_to, $example_to
                );
                //
                //Add the meaning and example for the word to translate from.
                CreateController::create_meaning_example($term_id, $word_from, $language_from_id, $meaning_from, $example_from);
                //
                //Add the meaning and example for the word to translate to.
                CreateController::create_meaning_example($term_id, $word_to, $language_to_id, $meaning_to, $example_to);
            }
        }
    }
}
