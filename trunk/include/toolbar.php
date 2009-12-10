<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class toolbar {
//    private $id;
    private $id_group;
    private $id_resultset;
    private $tools;


    function __construct($id_group, $id_resultset, $tools) {
//        $this->id = $id;
        $this->id_group = $id_group;
        $this->id_resultset = $id_resultset;
        $this->tools = $tools;
    }

//    function get_id() {
//        return $this->id;
//    }

    function get_id_group() {
        return $this->id_group;
    }

    function get_id_resultset() {
        return $this->id_resultset;
    }

    function get_tools() {
        return $this->tools;
    }
}
?>
