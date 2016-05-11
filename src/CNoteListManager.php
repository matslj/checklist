<?php

/**
 * Description of CNoteListManager.
 * Manages lists of lists of notes. All database handling regarding
 * the CNoteList goes through this class.
 *
 * @author Mats Ljungquist
 */
class CNoteListManager {
    
    public function __construct() {
    }
    
    /**
     * Retrieves a list of a list of notes from the database. The result
     * is sorted so that the default list (there should only be one) will
     * be returned first in the list, and then the rest is sorted by creation date.
     * 
     * @param type $dbRef
     * @return \CNoteList an array of CNoteLists
     */
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
            ORDER BY isDefaultNoteList DESC, created DESC;
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
    
    /**
     * Retrieves a single list of notes from the database using the id
     * of the list of notes.
     * 
     * @param type $dbRef db reference
     * @param type $listId the id of the list of notes that should be retrieved
     * @return type a single CNoteList
     */
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
    
    /**
     * Same as getNoteListsFromDB() but returns the result as Json.
     * 
     * @param type $dbRef
     * @return string
     */
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
    
    /**
     * Same as getNoteListFromDB but result returned as JSON.
     * 
     * @param type $dbRef
     * @param type $listId
     * @return type
     */
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
