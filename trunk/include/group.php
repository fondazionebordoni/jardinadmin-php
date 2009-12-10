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
class Group {
    private $id;
    private $name;
    private $status;

    function __construct($id, $name, $status) {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_status() {
        return $this->status;
    }

}
?>
