<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateController extends Controller
{
    //
    //Create the word, its synonym and translation. This will link them to the 
    //right term.
    static function create_word_translation_synonym($term_id, $language_id, $word){
        //
        //Create a new word in the word table.
        self::create_word($word);
        //
        //Get the word's id.
        $word_id = @DataController::get_word_id($word)['data'];
        //
        //The query that gets the data.
        self::create_translation_and_synonym($term_id, $language_id, $word_id);
    }
    //
    //Create a new term in the term table.
    static function create_term($term, $type){
        //
        //Save the word that is not already in the database.
        $term_id = DB::table('term')->insertGetId([
            'name' => $term,
            'type' => $type,
            'is_valid' => 0,
            'created_at' => now(),
        ]);
        //
        //Once the record is inserted, return a boolean value.
        if(($term_id)){
            return $term_id;
        }
        else{
            return $term_id;
        }  
    }
    //
    //Create a new word in the word table.
    function create_word($word){
        //
        //Save the word that is not already in the database.
        $result = DB::table('word')->insert([
            'name' => $word,
            'created_at' => now(),
        ]);
        //
        //Once the record is inserted, return a boolean value.
        if(($result)){
            return true;
        }
        else{
            return false;
        }  
    }
    //
    //Create a new translation using the language and the word's term.
    function create_translation_and_synonym($term_id, $language_id, $word_id){
        //
        //Get the id of the translation inserted, the id will be used later on.
        $translation_id = DB::table('translation')->insertGetId([
            'term' => $term_id,
            'language' => $language_id,
            'created_at' => now(),
        ]);
        //
        //Save the data in the synonym table using the translation id acquired earlier on.
        $result = DB::table('synonym')->insert([
            'word' => $word_id,
            'translation' => $translation_id,
            'is_valid' => 0,
            'created_at' => now(),
        ]);
    }
    //
    //Add both meaning and example in the database.
    static function create_meaning_example($term_id, $word, $language_id, $meaning, $example){
        //
        //Create the meaning.
        self::create_meaning($term_id, $word, $language_id, $meaning);
        //
        //Create the example.
        self::create_example($term_id, $word, $language_id, $example);
    }
    //
    //Add the meaning for the word.
    function create_meaning($term_id, $word, $language_id, $meaning){
        //
        //Ensure we have a value before saving it.
        if(!empty($meaning)){
            //
            //Get the translation's id.
            $translation_id = @DataController::get_translation_id($term_id, $language_id, $word)['data'];
            //
            //Save the meaning in the database.
            $result = DB::table('definition')->insert([
                'meaning' => $meaning,
                'translation' => $translation_id,
                'created_at' => now(),
            ]);
        }
        else{
            echo "Meaning not provided, it is impossible to reach this point";
        }
    }
    //
    //Add the example for the word.
    function create_example($term_id, $word, $language_id, $example){
        //
        //Ensure we have a value before saving it.
        if(!empty($example)){
            //
            //Get the translation's id.
            $translation_id = @DataController::get_translation_id($term_id, $language_id, $word)['data'];
            //
            //Save the meaning in the database.
            $result = DB::table('examples')->insert([
                'sentence' => $example,
                'translation' => $translation_id,
                'created_at' => now(),
            ]);
        }
    }
}
