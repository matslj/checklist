<?php

/**
 * Description of CNote
 *
 * @author Mats Ljungquist
 */
class CNote {
    
    private $id;
    private $text;    // The text in the note
    private $checked; // Boolean value - true if the not is checked. False otherwise.
    private $date;    // The date when the note was created
    private $updater; // The userid of the updater/creator
    private $tag;     // The group name of the note; every note with the same 'tag' is considered to be within the same group.
    
    public function __construct($id, $text, $checked, $date, $updater, $tag) {
        $this->id = $id;
        $this->text = $text;
        $this->checked = $checked;
        $this->date = $date;
        $this->updater = $updater;
        $this->tag = $tag;
    }

    public function getId() {
        return $this->id;
    }

    public function getTag() {
        return $this->tag;
    }
    
    public function getText() {
        return $this->text;
    }

    public function getChecked() {
        return $this->checked;
    }

    public function getDate() {
        return $this->date;
    }

    public function getUpdater() {
        return $this->updater;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function setChecked($checked) {
        $this->checked = $checked;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setUpdater($updater) {
        $this->updater = $updater;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }

        
    public function toJson() {
        
        return array(
            "id" => $this->id,
            "text" => $this->text,
            "checked" => $this->checked,
            "date" => $this->date,
            "updater" => $this->updater,
            "tag" => $this->tag,
        );
    }
}
