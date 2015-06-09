<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CNoteManager
 *
 * @author Mats Ljungquist
 */
class CNoteManager {
    //put your code here
    
    private $test = false;
    private $notes;
    
    public function __construct() {
        $this -> notes = array();
        if ($this -> test) {
            $temp = new CNote(1, "Tandborste", false, time(), "Mats", "toalett");
            $this -> notes[] = $temp;
            
            $temp = new CNote(2, "Tandkräm", false, time(), "Mats", "toalett");
            $this -> notes[] = $temp;
            
            $temp = new CNote(3, "Kam", false, time(), "Mats", "toalett");
            $this -> notes[] = $temp;
            
            $temp = new CNote(4, "Hårskum", false, time(), "Mats", "toalett");
            $this -> notes[] = $temp;
            
            $temp = new CNote(5, "Deoderant", false, time(), "Mats", "toalett");
            $this -> notes[] = $temp;
            
            $temp = new CNote(6,"Tvål", false, time(), "Mats", "toalett");
            $this -> notes[] = $temp;
        }
    }
    
    public static function deleteNote($dbRef, $id) {
        $msg = null;
        $spDeleteNote = DBSP_DeleteNote;
        $query = "CALL {$spDeleteNote}({$id});";
        $res = $dbRef->MultiQuerySpecial($query);
        $nrOfStatements = $dbRef->RetrieveAndIgnoreResultsFromMultiQuery();
        if($nrOfStatements != 1) {
            $msg = "Fel: kunde inte radera notering";
        }
        return $msg;
    }
    
    public static function checkUncheckNote($dbRef, $id, $value) {
        $msg = null;
        $boolValue = ($value == 1 ? "true" : "false");
        $spCheckUncheckNote = DBSP_CheckUncheckNote;
        $query = "CALL {$spCheckUncheckNote}({$id}, {$boolValue});";
        $res = $dbRef->MultiQuerySpecial($query);
        $nrOfStatements = $dbRef->RetrieveAndIgnoreResultsFromMultiQuery();
        if($nrOfStatements != 1) {
            $msg = "Fel: kunde inte markera/avmarkera notering";
        }
        return $msg;
    }
    
    public function getNotes() {
        return $this->notes;
    }
    
    public static function addNote($dbRef, $text, $tag) {
        $msg = "";
        
        $spCreateNote = DBSP_CreateNote;
        $query = "CALL {$spCreateNote}('{$text}', '{$tag}');";
        
        // Perform the query
        $res = $dbRef->MultiQuery($query);
        if ($res != null && $res != false) {
            // Ignore results but count successful statements.
            $nrOfStatements = $dbRef->RetrieveAndIgnoreResultsFromMultiQuery();
            if($nrOfStatements != 1) {
                $msg .= "Fel: kunde inte registrera/avregistrera din markering";
            }
        }

        return $msg;
    }
    
    public static function getNotesFromDB($dbRef) {
        $tNote = DBT_Note;
        
        $query = <<< EOD
            SELECT
                idNote,
                textNote,
                tagNote,
                dateNote,
                checkedNote
            FROM {$tNote}
            ORDER BY tagNote ASC, textNote ASC;
EOD;

        // Perform the query and manage results
        $result = $dbRef->Query($query);
        $notes = array();

        while($row = $result->fetch_object()) {
            $notes[] = new CNote($row -> idNote, $row -> textNote, $row -> checkedNote == 1, $row -> dateNote, "", $row -> tagNote);
        }
        $result -> close();
        
        return $notes;
    }
    
    public function getNotesAsJson($dbRef = null) {
        $tempArray = null;
        
        if ($dbRef === null) {
            // php assigns arrays by copy (not deep copy though)
            $tempArray = $this -> notes;
        } else {
            $tempArray = self::getNotesFromDB($dbRef);
        }
        
        if (empty($tempArray)) {
            return "[]";
        } else {
            foreach ($tempArray as &$value) {
                $value = $value->toJson();
            }
            return json_encode($tempArray);
        }
    }

}
