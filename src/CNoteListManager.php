<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CNoteListManager
 *
 * @author Mats Ljungquist
 */
class CNoteListManager {
    
    public function __construct() {
    }
    
    public static function getNoteListsFromDB($dbRef) {
        $tNoteList = DBT_NoteList;
        
        $query = <<< EOD
            SELECT
                idNoteList as id,
                titleNoteList as title,
                descriptionNoteList as description,
                created,
                isDefaultNoteList as def
            FROM {$tNoteList}
            ORDER BY isDefaultNoteList ASC, created ASC;
EOD;

        // Perform the query and manage results
        $result = $dbRef->Query($query);
        $noteLists = array();

        while($row = $result->fetch_object()) {
            $noteLists[] = new CNoteList($row -> id, $row -> title, $row -> description, $row -> created, $row -> def == 1);
        }
        $result -> close();
        
        return $noteLists;
    }
    
    public static function getNoteListFromDB($dbRef, $listId) {
        $tNoteList = DBT_NoteList;
        
        $query = <<< EOD
            SELECT
                idNoteList as id,
                titleNoteList as title,
                descriptionNoteList as description,
                created,
                isDefaultNoteList as def
            FROM {$tNoteList}
            WHERE idNoteList = {$listId};
EOD;

        // Perform the query and manage results
        $result = $dbRef->Query($query);
        $noteList = $result->fetch_object();
        $result -> close();
        
        return $noteList;
    }
    
    public function getNoteListsAsJson($dbRef = null) {
        $tempArray = null;
        
        if ($dbRef === null) {
            // php assigns arrays by copy (not deep copy though)
            $tempArray = $this -> notes;
        } else {
            $tempArray = self::getNoteListsFromDB($dbRef);
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
    
    public function getNoteListAsJson($dbRef = null, $listId) {
        $tempNoteList = self::getNoteListFromDB($dbRef, $listId);
        $tempNoteList = $tempNoteList -> toJson();
        return json_encode($tempNoteList);
    }

    public static function addNoteList($dbRef, $title, $description, $copyId) {
        $msg = "";
        $noteLists = null;
        $lastId = -1;
        
        $spCreateNoteList = DBSP_CreateNoteList;
        $query = <<< EOD
            SET @aListId = {$lastId};
            CALL {$spCreateNoteList}('{$title}', '{$description}', @aListId);
            SELECT @aListId AS id;
EOD;
        
        // Perform the query
        $res = $dbRef->MultiQuery($query);
        if ($res != null && $res != false) {
            $results = Array();
            $dbRef->RetrieveAndStoreResultsFromMultiQuery($results);
            $row = $results[2]->fetch_object();
            $lastId = $row->id;
            $results[2]->close();
        }

        // Preload list with values from other note list
        if (empty($msg) && $lastId >= 0 && $copyId >= 0) {
            
            $noteLists = CNoteManager::getNotesFromDB($dbRef, $copyId);
            
            foreach ($noteLists as $value) {
                CNoteManager::addNote($dbRef, $value->getText(), $value->getTag(), $lastId);
            }
        }

        return $msg;
    }
    
    public static function updateNoteList($dbRef, $title, $description, $default, $noteListId) {
        $msg = "";
        
        $spUpdateNoteList = DBSP_UpdateNoteList;
        $query = "CALL {$spUpdateNoteList}({$noteListId}, '{$title}', '{$description}', {$default});";
        
        // Perform the query
        $res = $dbRef->MultiQuery($query);
        if ($res != null && $res != false) {
            // Ignore results but count successful statements.
            $nrOfStatements = $dbRef->RetrieveAndIgnoreResultsFromMultiQuery();
            if($nrOfStatements != 1) {
                $msg .= "Fel: kunde inte updatera notelist med id '{$noteListId}'";
            }
        }

        return $msg;
    }
    
    public static function deleteNoteList($dbRef, $id) {
        $msg = null;
        $udfDeleteNoteList = DBUDF_DeleteNoteList;
        $query = "SELECT {$udfDeleteNoteList}({$id}) AS status;";

        $res = $dbRef->MultiQuery($query);
        if ($res != null && $res != false) {
             $results = Array();
            // Hämta resultatet från queryn och lägg in det i result-arrayen.
            // Vi kollar sen om status != 1 (se definitionen av SQLen ovan)
            $dbRef->RetrieveAndStoreResultsFromMultiQuery($results);
            // $log -> debug(print_r($results, true));
            $row = $results[0]->fetch_object();
            if ($row->status == 1) {
                $msg = "Kategorin innehåller bilder och kan därför inte raderas. Radera bilderna i kategorin först.";
            }
            $results[0] -> close();
        }
        return $msg;
    }
}
