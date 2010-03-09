<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pluginassociation
 *
 * @author acozzolino
 */
class Pluginassociation {
    //put your code here
    private $id_plugin;
    private $id_resultset;
    private $id_group;

    function __construct($id_plugin, $id_resultset, $id_group) {
        $this->id_plugin = $id_plugin;
        $this->id_resultset = $id_resultset;
        $this->id_group = $id_group;
    }

    public function get_idresultset() {
        return $this->id_resultset;
    }

    public function get_idplugin() {
        return $this->id_plugin;
    }

    public function get_idgroup() {
        return $this->id_group;
    }
}
?>
