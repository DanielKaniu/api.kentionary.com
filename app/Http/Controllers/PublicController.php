<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//
//Useful for interrogating the database.
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Get the request data to use to check if a word exists in the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function checker(Request $request)
    {
        //
        //The word to check.
        $word = $request['word'];
        //
        //The word's language.
        $language = $request['language'];
        //
        //Start the checking process.
        //
        //Store the data retrieved from the database.
        $result = DB::table('term')
            ->join('translation', 'translation.term', '=', 'term.term')
            ->leftjoin('definition', 'definition.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->join('synonym', 'synonym.translation', '=', 'translation.translation')
            ->join('word', 'synonym.word', '=', 'word.word')
            ->select('translation.translation')
            ->distinct()
            ->where('word.name', '=', $word)
            ->where('language.name', '=', $language)
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
            return '0';
        }
    }
    /**
     * Perform the translation.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_translation(Request $request)
    {
        //
        //The word to translate.
        $word = $request['word'];
        //
        //The language whose word to translate.
        $language = $request['language'];
        //
        //The translation.
        $translation = DB::select(DB::raw
            ("SELECT 
                    result.term,
                    result.category,
                    JSON_OBJECTAGG(result.language, result.translation) as translation
                FROM(
                    SELECT 
                        term.name as term, 
                        language.name as language, 
                        term.type as category,
                        JSON_ARRAYAGG(word.name) as translation
                    FROM language
                    INNER JOIN translation on translation.language = language.language
                    INNER JOIN term on translation.term = term.term
                    INNER JOIN synonym on synonym.translation = translation.translation
                    INNER JOIN word on synonym.word = word.word
                    INNER JOIN (
                        SELECT word.name, term.term
                        FROM word
                        INNER JOIN synonym on synonym.word = word.word
                        INNER JOIN translation on synonym.translation = translation.translation
                        INNER JOIN language on translation.language = language.language
                        INNER JOIN term on translation.term = term.term
                        WHERE word.name = '$word'
                        AND language.name = '$language'
                    ) AS search ON search.term = term.term
                    GROUP BY term.name, language.name, term.type
                ) as result
                GROUP BY result.term, result.category"
            ));
        //
        //Check if we have a translation or not.
        if(!empty($translation)){
            //
            //Formulate the response to return to the application that made the request.
            return response()->json([
                'data' => $translation,
                'message' => 'Translation received successfully.',
                'ok' => true
            ]);
        }
        //
        //At this point there is no translation.
        else{
            //
            //Formulate the response to return to the application that made the request.
            return response()->json([
                'message' => 'Translation unavailable.',
                'ok' => false
            ]);
        }
    }
    /**
     * Help the user add a new word to the database.
     */
    public function add_word(Request $request){
        //
        //Get the word to add.
        $word = $request['word'];
        //
        //The word's translation.
        $translation = $request['translation'];
        //
        //The word's language.
        $language = $request['language'];
        //
        //First check if the word exists in the database.
        $word_exists = CheckController::check_word_exists($word);
        //
        //If the word exists, you can proceed as necessary.
        if($word_exists === true){
            //
            //Check if the word is linked to a term.
            $word_linked = CheckController::check_word_link_term($word, $language);
            //
            //At this point, the word is already linked to a term.
            if($word_linked === true){
                //
                //The translation.
                $term = DB::select(DB::raw
                    (
                        "SELECT DISTINCT term.name, term.type
                        FROM word
                        INNER JOIN synonym on synonym.word = word.word
                        INNER JOIN translation on synonym.translation = translation.translation
                        INNER JOIN language on translation.language = language.language
                        INNER JOIN term on translation.term = term.term
                        WHERE word.name = '$word'
                        AND language.name = '$language'"
                    )
                );
                //
                //Check if we have a translation or not.
                if(!empty($term)){
                    //
                    //Formulate the response to return to the application that made the request.
                    return response()->json([
                        'data' => $term,
                        'message' => 'Term(s) received successfully.',
                        'ok' => true
                    ]);
                }
                //
                //At this point there is no translation.
                else{
                    //
                    //Formulate the response to return to the application that made the request.
                    return response()->json([
                        'message' => 'Term unavailable.',
                        'ok' => false
                    ]);
                }
            }
            //
            //At this point, the word is not linked to a term.
            else{
                //
                //Link the word.

            }
        }
        //
        //At this point, the word does not exist in the database.
        else{
            //
            //Add the word to the database.

            //
            //Link the word to a term.
        }
        //
        return $request;
    }
}
