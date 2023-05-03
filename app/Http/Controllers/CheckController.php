<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//
//Useful for interrogating the database.
use Illuminate\Support\Facades\DB;
//
//Useful when using join statements in SQL.
use Illuminate\Database\Query\JoinClause;

class CheckController extends Controller
{
    //
    //Declare the vriables that compose a translation.
    public $translate_from;
    public $translate_to;
    public $synonym;
    public $language_from;
    public $language_to;
    public $language_synonym;
    public $term;
    /**
     * Get the term associated with the provided words.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_term(Request $request)
    {
        //
        //Get the words that will help in searching for terms.
        $word_one = $request['word']['word'][0];
        $word_two = $request['word']['word'][1];
        //
        //This is the synonym.
        //I am using the @ symbol to prevent php from complaining if no synonym is provided.
        $word_three = @$request['word']['word'][2];
        //
        //Get the terms from the database.
        $terms = DB::table('term')
            ->join('translation', 'translation.term', '=', 'term.term')
            ->leftjoin('definition', 'definition.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->join('synonym', 'synonym.translation', '=', 'translation.translation')
            ->join('word', 'synonym.word', '=', 'word.word')
            ->select('term.name as object', 'term.type as category')
            ->distinct()
            ->where('word.name', '=', $word_one)
            ->orWhere('word.name', '=', $word_two)
            ->orWhere('word.name', '=', $word_three)
            ->get();
        //
        //Check if we get some data from the database.
        if(!empty($terms)){
            //
            //Formulate the response to return to the application that made the request..
            return response()->json([
                'data' => $terms,
                'message' => 'Data received successfully.',
                'ok' => true
            ]);
        }
        //
        //At this point there is no data.
        else{
            //
            //Formulate the response to return to the application that made the request.
            return response()->json([
                'message' => 'Terms unavailable.',
                'ok' => false
            ]);
        }

    }
    /**
     * Get the request data to use to check if words exist in the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_request(Request $request)
    {
        //
        //Destructure the request.
        $word = $request['values'];
        //
        //The word & language to translate from.
        $this->translate_from = $word['translate_from']['word'];
        $this->language_from = $word['translate_from']['language'];
        //
        //The word & language to translate to.
        $this->translate_to = $word['translate_to']['word'];
        $this->language_to = $word['translate_to']['language'];
        //
        //The synonym & language to contribute.
        $this->synonym = $word['synonym']['word'];
        $this->language_synonym = $word['synonym']['language'];
        //
        //The term selected to link the word(s) to translate.
        $this->term = $word['term'];
        //
        //Start the checking process.
        $this->execute_synonym();
    }
    /*
    * Conduct the main checking process.
    */
    public function execute_synonym(){
        //
        //The results from interrogating the database.
        $translate_from = $this -> check_translate_from();
        $translate_to = $this -> check_translate_to();
        $synonym = $this -> check_synonym();
        //
        //Check if there are links between words and a term.
        //
        //Translations already exist in the database.
        if(
            $translate_from === true && 
            $translate_to === true &&
            $synonym === true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'none'
                    ]
                );
        }
        //
        //Translation_from, translation_to and synonym don't exit in the database.
        elseif(
            $translate_from !== true && 
            $translate_to !== true &&
            $synonym !== true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'all'
                    ]
                );
        }
        //
        //Translation_to and synonym in the database not in database.
        elseif(
            $translate_from === true && 
            $translate_to !== true &&
            $synonym !== true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_to_synonym'
                    ]
                );
        }
        //
        //Translation_from and synonym not in the database.
        elseif(
            $translate_from !== true && 
            $translate_to === true &&
            $synonym !== true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_from_synonym'
                    ]
                );
        }
        //
        //Translation_from and translation_to not in the database.
        elseif(
            $translate_from !== true && 
            $translate_to !== true &&
            $synonym === true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_from_to'
                    ]
                );
        }
        //
        //Synonym not in the database.
        elseif(
            $translate_from === true && 
            $translate_to == true &&
            $synonym !== true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'synonym'
                    ]
                );
        }
        //
        //Translation_to not in the database.
        elseif(
            $translate_from === true && 
            $translate_to !== true &&
            $synonym === true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_to'
                    ]
                );
        }
        //
        //Translation_from not in the database.
        elseif(
            $translate_from !== true && 
            $translate_to === true &&
            $synonym === true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_from'
                    ]
                );
        }
    }
    //
    //Check if the word to translate is linked to a term.
    function check_translate_from(){
        //
        //Store the data retrieved from the database.
        $result = DB::table('term')
            ->join('translation', 'translation.term', '=', 'term.term')
            ->leftjoin('definition', 'definition.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->join('synonym', 'synonym.translation', '=', 'translation.translation')
            ->join('word', 'synonym.word', '=', 'word.word')
            ->select('term.name as object', 'term.type as category')
            ->distinct()
            ->where('word.name', '=', $this->translate_from)
            ->where('term.name', '=', $this->term)
            ->where('language.name', '=', $this->language_from)
            ->get();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            return true;
        }
        else{
            //
            return false;
        }
    }
    //
    //Check if the word to translate is linked to a term.
    function check_translate_to(){
        //
        //Store the data retrieved from the database.
        $result = DB::table('term')
            ->join('translation', 'translation.term', '=', 'term.term')
            ->leftjoin('definition', 'definition.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->join('synonym', 'synonym.translation', '=', 'translation.translation')
            ->join('word', 'synonym.word', '=', 'word.word')
            ->select('term.name as object', 'term.type as category')
            ->distinct()
            ->where('word.name', '=', $this->translate_to)
            ->where('term.name', '=', $this->term)
            ->where('language.name', '=', $this->language_to)
            ->get();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            return true;
        }
        else{
            //
            return false;
        }
    }
    //
    //Check if the synonym is linked to a term.
    function check_synonym(){
        //
        //Store the data retrieved from the database.
        $result = DB::table('term')
            ->join('translation', 'translation.term', '=', 'term.term')
            ->leftjoin('definition', 'definition.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->join('synonym', 'synonym.translation', '=', 'translation.translation')
            ->join('word', 'synonym.word', '=', 'word.word')
            ->select('term.name as object', 'term.type as category')
            ->distinct()
            ->where('word.name', '=', $this->synonym)
            ->where('term.name', '=', $this->term)
            ->where('language.name', '=', $this->language_synonym)
            ->get();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            return true;
        }
        else{
            //
            return false;
        }
    }
    /*
    * ===== CHECK IF A WORD ALREADY EXISTS IN THE DATABASE. =====
    */
    //
    //Check if the word already exists in the database.
    static function check_if_word_exists($word){
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
            return true;
        }
        else{
            //
            //Return a boolean value that will help take further action.
            return false;
        }
    }
    //
    //Check if the term already exists in the database.
    static function check_if_term_exists($term, $type){
        //
        //Store the data retrieved from the database.
        $result = DB::table('term')
            ->select('term.name')
            ->distinct()
            ->where('term.name', $term)
            ->where('term.type', $type)
            ->get()
            ->toArray();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            //Return a boolean value that will help take further action.
            return true;
        }
        else{
            //
            //Return a boolean value that will help take further action.
            return false;
        }
    }
    /**
     * 
     * ===== YES TERM, NO SYNONYM =====
     * 
     * Get the request data to use to check if words exist in the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_request_no_synonym(Request $request)
    {
        //
        //Destructure the request.
        $word = $request['values'];
        //
        //The word & language to translate from.
        $this->translate_from = $word['translate_from']['word'];
        $this->language_from = $word['translate_from']['language'];
        //
        //The word & language to translate to.
        $this->translate_to = $word['translate_to']['word'];
        $this->language_to = $word['translate_to']['language'];
        //
        //The term selected to link the word(s) to translate.
        $this->term = $word['term'];
        //
        //Start the checking process.
        $this->execute_no_synonym();
    }
    /**
     * 
     * ===== NO SYNONYM =====
     * 
     * Conduct the main checking process.
    */
    public function execute_no_synonym(){
        //
        //The results from interrogating the database.
        $translate_from = $this -> check_translate_from();
        $translate_to = $this -> check_translate_to();
        //
        //Check if there are links between words and a term.
        //
        //Translations already exist in the database.
        if($translate_from === true && $translate_to === true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'none'
                    ]
                );
        }
        //
        //Translation_to in the database not in database.
        elseif($translate_from === true && $translate_to !== true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_to'
                    ]
                );
        }
        //
        //Translation_from not in the database.
        elseif($translate_from !== true && $translate_to === true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_from'
                    ]
                );
        }
        //
        //Translation_from and translation_to not in the database.
        elseif($translate_from !== true && $translate_to !== true){
                //
                echo json_encode(
                    [
                        "ok" => true,
                        "data" => 'translation_from_to'
                    ]
                );
        }
    }
    /**
     * 
     * ===== NEW TERM, NO SYNONYM =====
     * 
     * Get the request data to use to check if words exist in the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_request_term_no_synonym(Request $request)
    {
        echo 'New term, no synonym';
        // //
        // //Destructure the request.
        // $word = $request['values'];
        // //
        // //The word & language to translate from.
        // $this->translate_from = $word['translate_from']['word'];
        // $this->language_from = $word['translate_from']['language'];
        // //
        // //The word & language to translate to.
        // $this->translate_to = $word['translate_to']['word'];
        // $this->language_to = $word['translate_to']['language'];
        // //
        // //The synonym & language to contribute.
        // $this->synonym = $word['synonym']['word'];
        // $this->language_synonym = $word['synonym']['language'];
        // //
        // //The term selected to link the word(s) to translate.
        // $this->term = $word['term'];
        // //
        // //Start the checking process.
        // $this->execute();
    }
    /*
    |--------------------------------------------------------------------------
    | PUBLIC API
    |--------------------------------------------------------------------------
    */
    //
    //Check if a word that a user wants to add is linked to a term.
    static function check_word_link_term($word, $language){
        //
        //Store the data retrieved from the database.
        $result = DB::table('term')
            ->join('translation', 'translation.term', '=', 'term.term')
            ->leftjoin('definition', 'definition.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->join('synonym', 'synonym.translation', '=', 'translation.translation')
            ->join('word', 'synonym.word', '=', 'word.word')
            ->select('term.name as object', 'term.type as category')
            ->distinct()
            ->where('word.name', '=', $word)
            ->where('term.name', '=', $term)
            ->where('language.name', '=', $language)
            ->get();
        //
        //Count the results to see if there is some data.
        if(count($result) >= 1){
            //
            return true;
        }
        else{
            //
            return false;
        }
    }
}
