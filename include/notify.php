<?php

/**
 * Description of resultset
 *
 * @author mavellino
 */
class Notify {

    private $id_resultset;
    private $notify_name;
    private $address_statement;
    private $data_statement;
    private $xslt;
    private $id_notify;
    private $bmdid;

    function __construct($id_resultset, $notify_name, $address_statement, $data_statement, $xslt, $id_notify, $bmdid) {
        $this->id_resultset = $id_resultset;
        $this->notify_name = $notify_name;
        $this->address_statement = $address_statement;
        $this->data_statement = $data_statement;
        $this->xslt = $xslt;
        $this->id_notify = $id_notify;
        $this->bmdid = $bmdid;
    }

    function get_id_resultset(){
    	return $this->id_resultset;
    }
    function get_notify_name(){
    	return $this->notify_name;
    }
    function get_address_statement(){
    	return $this->address_statement;
    }
    function get_data_statement(){
    	return $this->data_statement;
    }
    function get_xslt(){
    	return $this->xslt;
    }
    function get_id_notify(){
    	return $this->id_notify;
    }
    function get_bmdid(){
    	return $this->bmdid;
    }
}

?>
