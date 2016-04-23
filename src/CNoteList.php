<?php

/**
 * Description of CNoteList (a holder of groups of CNotes connected to different
 * events)
 *
 * @author Mats Ljungquist
 */
class CNoteList {
    
    private $id;
    private $title;       // The title of the note list
    private $description; // An optional description of the note list
    private $created;     // the date when the note list was first created
    private $default;     // if set as true it is the default note list
    
    public function __construct($id, $title, $description, $created, $default) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->created = $created;
        $this->default = $default;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getDefault() {
        return $this->default;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCreated($created) {
        $this->created = $created;
    }

    public function setDefault($default) {
        $this->default = $default;
    }
        
    public function toJson() {
        
        return array(
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "created" => $this->created,
            "def" => $this->default,
        );
    }
}
