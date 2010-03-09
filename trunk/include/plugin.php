<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of group
 *
 * @author gpantanetti
 */
class Plugin {
    private $id;
    private $name;
    private $configurationfile;
    private $type;
    private $note;

    function __construct($id, $name, $configurationfile, $type, $note) {
        $this->id = $id;
        $this->name = $name;
        $this->configurationfile = $configurationfile;
        $this->type = $type;
        $this->note = $note;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }


    public function get_configurationfile() {
        return $this->configurationfile;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_note() {
        return $this->note;
    }

}
?>