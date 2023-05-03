<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class DataController extends Controller
{
    //
    //Get the word's id.
    static function get_word_id($word){
        //
        //Store the data retrieved from the database.
        $result = DB::table('word')
            ->select('word.word')
            ->where('word.name', $word)
            ->get()
            ->toArray();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            //Return a boolean value that will help take further action.
            return [
                "success" => true,
                "data" => $result[0]->word
            ];
        }
        else{
            //
            //Return a boolean value that will help take further action.
            return ["success" => false];
        }
    }
    //
    //Get the language's id.
    static function get_language_id($language){
        //
        //Store the data retrieved from the database.
        $result = DB::table('language')
            ->select('language.language')
            ->where('language.name', $language)
            ->get()
            ->toArray();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            //Return a boolean value that will help take further action.
            return [
                "success" => true,
                "data" => $result[0]->language
            ];
        }
        else{
            //
            //Return a boolean value that will help take further action.
            return ["success" => false];
        }
    }
    //
    //Get the term's id
    static function get_term_id($term){
        //
        //Store the data retrieved from the database.
        $result = DB::table('term')
            ->select('term.term')
            ->where('term.name', $term)
            ->get()
            ->toArray();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            //Return a boolean value that will help take further action.
            return [
                "success" => true,
                "data" => $result[0]->term
            ];
        }
        else{
            //
            //Return a boolean value that will help take further action.
            return ["success" => false];
        }
    }
    //
    //Get the translation's id.
    static function get_translation_id($term_id, $language_id, $word){
        //
        $result = DB::table('term')
            ->join('translation', 'translation.term', '=', 'term.term')
            ->leftjoin('definition', 'definition.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->join('synonym', 'synonym.translation', '=', 'translation.translation')
            ->join('word', 'synonym.word', '=', 'word.word')
            ->select('translation.translation')
            ->distinct()
            ->where('term.term', '=', $term_id)
            ->where('language.language', '=', $language_id)
            ->where('word.name', '=', $word)
            ->get()
            ->toArray();
        //
        //Check if we get some data from the database.
        if(!empty($result)){
            //
            return [
                "success" => true,
                "data" => $result[0]->translation
            ];
        }
        //
        //At this point there is no data.
        else{
            //
            //Return a boolean value that will help take further action.
            return ["success" => false];
        }
    }
}
