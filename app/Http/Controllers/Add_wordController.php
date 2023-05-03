<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
//
//Save the new translation in the database.
// $save_class = new SaveController();

class Add_wordController extends Controller
{
    //
    //Declare the vriables that compose a translation.
    public $translation_from;
    public $translation_to;
    public $synonym;
    public $term;
    public $type;
    public $synonym_state;
    //
    //The state of a method, if it has executed or not;
    public $mtd_state = false;
    /**
     * Get the list of categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_category()
    {
        //
        //The languages.
        $categories = DB::table('term')->select('type as category')->distinct()->where('term.type', '!=', NULL)->get();
        //
        //Check if we get some data from the database.
        if(!empty($categories)){
            //
            //Formulate the response to return to the application that made the request.
            return response()->json([
                'data' => $categories,
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
                'message' => 'Categories unavailable.',
                'ok' => false
            ]);
        }

    }

    /**
     * Get the request data to use to save the new translations in the database.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_request(Request $request)
    {
        //
        //Destructure the request.
        $translation = $request['values'];
        //
        //Get the components of the translation from object.
        $this->translation_from = $translation['translation_from'];
        $this->translation_to = $translation['translation_to'];
        @$this->synonym = $translation['synonym'];
        $this->term = $translation['term'];
        $this->type = $translation['type'];
        $this->synonym_state = $translation['synonym_state'];
        //
        //Start the saving process.
        $this->execute();
    }
    //
    //Facilitate the process of adding a new word depending on some various parameters.
    function execute(){
        //
        //Check if the user has provided some synonyms.
        //
        //At this point the user has provided a synonym; the term is also not new.          
        if ($this->synonym_state === true && $this->term['new_term'] !== true) {
            //
            //Loop through the types to add to the database.
            switch($this->type){
                //
                //Save the translation_from, translation_to and synonym. Link them to the 
                //selected term.
                case 'all':
                    //
                    SaveController::save_all(
                        $this->translation_to, $this->translation_from, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                //Save the translation_to and synonym in the database, link them with the 
                //selected term.
                case 'translation_to_synonym':
                    //
                    SaveController::save_translation_to_synonym(
                        $this->translation_to, $this->translation_from, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                //Save the translation_from and synonym in the database, link them with the 
                //selected term.
                case 'translation_from_synonym':
                    //
                    SaveController::save_translation_from_synonym(
                        $this->translation_to, $this->translation_from, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                //Save the translation_from and translation_to in the database, link them with the 
                //selected term.
                case 'translation_from_to':
                    //
                    SaveController::save_translation_from_to(
                        $this->translation_to, $this->translation_from, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                //Save the synonym in the database, link it with the selected term.
                case 'synonym':
                    //
                    SaveController::save_synonym(
                        $this->translation_to, $this->translation_from, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    break;
                //
                //Save the translation_to in the database, link it with the selected term.
                case 'translation_to':
                    //
                    SaveController::save_translation_to(
                        $this->translation_to, $this->translation_from, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                       //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    break;
                //
                //Save the word to translate_from in the database, link it with the selected term.
                case 'translation_from':
                    //
                    SaveController::save_translation_from(
                        $this->translation_from, $this->translation_to, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                default:
                    //
                    //It is extremely hard to reach this point.
                    //
                    //Return false.
                    echo json_encode(["ok" => false]);
                    //
                    break;
            }
        }
        //
        //At this point the user has provided a synonym; the term is new.          
        if ($this->synonym_state === true && $this->term['new_term'] === true) {
            //
            //Loop through the types to add to the database.
            switch($this->type){
                //
                //Save the translation_from, translation_to and synonym. Link them to the 
                //selected term.
                case 'all':
                    //
                    SaveController::save_all(
                        $this->translation_to, $this->translation_from, $this->synonym, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                default:
                    //
                    //It is extremely hard to reach this point.
                    //
                    //Return false.
                    echo json_encode(["ok" => false]);
                    //
                    break;
            }
        }
        //
        //At this point there are no synonyms provided, and the term is not new.
        elseif($this->synonym_state !== true && $this->term['new_term'] !== true){
            //
            //Loop through the types to add to the database.
            switch($this->type){
                //
                //Save the translation_from and translation_to in the database, link them with the 
                //selected term.
                case 'translation_from_to':
                    //
                    SaveController::save_translation_from_to_no_synonym(
                        $this->translation_to, $this->translation_from, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                //Save the translation_to in the database, link it with the selected term.
                case 'translation_to':
                    //
                    SaveController::save_translation_to_no_synonym(
                        $this->translation_to, $this->translation_from, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                       //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    break;
                //
                //Save the word to translate_from in the database, link it with the selected term.
                case 'translation_from':
                    //
                    SaveController::save_translation_from_no_synonym(
                        $this->translation_from, $this->translation_to, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                default:
                    //
                    //It is extremely hard to reach this point.
                    //
                    //Return false.
                    echo json_encode(["ok" => false]);
                    //
                    break;
            }
        }
        //
        //At this point there no synonyms provided, and the term is new.
        elseif($this->synonym_state !== true && $this->term['new_term'] === true){
            //
            //Loop through the types to add to the database.
            switch($this->type){
                //
                //Save the translation_from and translation_to in the database, link them with the 
                //selected term.
                case 'translation_from_to':
                    //
                    SaveController::save_new_term_translation_from_to_no_synonym(
                        $this->translation_to, $this->translation_from, $this->term
                    );
                    //
                    //At this point the method has executed.
                    $this->mtd_state = true;
                    //
                    //Ensure the method is executed.
                    if($this->mtd_state === true){
                        //
                        //Send a response to the front-end.
                        echo json_encode(["ok" => true]);
                    }
                    else{
                        //
                        //Return false.
                        echo json_encode(["ok" => false]);
                    }
                    //
                    break;
                //
                default:
                    //
                    //It is extremely hard to reach this point.
                    //
                    //Return false.
                    echo json_encode(["ok" => false]);
                    //
                    break;
            }
        } 
    }
}