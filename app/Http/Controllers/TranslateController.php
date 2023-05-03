<?php

namespace App\Http\Controllers;
//
//The model.
use App\Models\Translate;
//
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;

class TranslateController extends Controller
{
    /**
     * Get the list of languages.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_language()
    {
        //
        //The languages.
        $languages = DB::table('language')->select('name')->get();
        //
        //Check if we get the list of languages or not.
        if(!empty($languages)){
            //
            //Return a response containing the languages.
            return response()->json([
                'data' => $languages,
                'message' => 'Data received successfully.',
                'ok' => true
            ]);
        }
        //
        //At this point we have no data.
        else{
            //.
            return response()->json([
                'message' => 'Languages unavailable.',
                'ok' => false
            ]);
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
     * Perform the translation from one language to another language specified by the user.
     *
     * @param  int  $request
     * @return \Illuminate\Http\Response
     */
    public function get_translation_filter(Request $request)
    {
        //
        //The word to translate.
        $word = $request['word'];
        //
        //The language whose word to translate.
        $language_from = $request['language_from'];
        //
        //The language whose word to translate.
        $language_to = $request['language_to'];
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
                        AND language.name = '$language_from'
                    ) AS search ON search.term = term.term
                    WHERE language.name = '$language_to'
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
     * Provide autosuggest words as the use types a letter.
     *
     * @param  string  $letter
     * @return \Illuminate\Http\Response
     */
    public function auto_suggest(Request $request)
    {
        //
        //The word to translate.
        $letter = $request['letter'];
        //
        //The language whose word to translate.
        $language = $request['language'];
        //
        //Get the autosuggest words.
        $word = DB::table('word')
            ->join('synonym', 'synonym.word', '=', 'word.word')
            ->join('translation', 'synonym.translation', '=', 'translation.translation')
            ->join('language', 'translation.language', '=', 'language.language')
            ->where('word.name', 'like', '%'.$letter.'%')
            ->where('language.name', '=', $language)
            ->select('word.name')
            ->get();
        //
        //Check if we get some data from the database.
        if(!empty($word)){
            //
            //Formulate the response to return to the application that made the request.
            return response()->json([
                'data' => $word,
                'message' => 'Autosuggest received successfully.',
                'ok' => true
            ]);
        }
        //
        //At this point there is no data.
        else{
            //
            //Formulate the response to return to the application that made the request.
            return response()->json([
                'message' => 'No autosuggest.',
                'ok' => false
            ]);
        }
    }
    /**
     * Translate words as requested by other developers.
     */
    public function translate_collection(Request $request){
        //
        //The word to translate.
        $language_from = $request['translate_from'];
        //
        //The language whose word to translate.
        $language_to = $request['translate_to'];
        //
        //The language whose word to translate.
        $data = $request['data'];  
        //
        //The container in which to store the translations.
        $translations = [];     
        //
        //Loop through the collection of words which a developer wants to
        //translate.
        foreach ($data as $key => $value) {
            //
            //Translate the individual words.
            $translation = $this->continue_translate($language_from, $language_to, $value);
            print_r(gettype($translation));
            //
            //Add the translations in the container.
            $translations[] = $translation['data'];
        }
        //
        //Formulate the response to return to the application that made the request.
        return response()->json([
            'data' => $translation,
            'ok' => true
        ]);        
    }
    /**
     * Translate words as requested by other developers.
     */
    public function continue_translate($language_from, $language_to, $word){
        //
        //The translation.
        $translation = DB::select(DB::raw
            ("SELECT 
                    result.translation
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
                        AND language.name = '$language_from'
                    ) AS search ON search.term = term.term
                    WHERE language.name = '$language_to'
                    GROUP BY term.name, language.name, term.type
                ) as result
                GROUP BY result.term, result.category"
        ));
        //
        //Formulate the response to return to the application that made the request.
        return response()->json([
            'data' => $translation
        ]);
    }
    
}
