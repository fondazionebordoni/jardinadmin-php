<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of group
 *
 * @author amuliello
 */
class Grouping {
    private $id;
    private $name;
    private $alias;

    function __construct($id, $name, $alias) {
        $this->id = $id;
        $this->name = $name;
        $this->alias = $alias;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_alias() {
        return $this->alias;
    }

}
?>
