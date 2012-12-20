<?php

include_once 'resource.php';
include_once 'db_utils.php';

/**
 * Description of resultset
 *
 * @author gpantanetti
 */
class Resultset extends Resource {

    private $statement;

    function __construct($id, $name, $alias, $statement) {
        parent::__construct($id, $name, $alias);
        $this->statement = $statement;
    }

    public function get_statement() {
        return $this->statement;
    } 
   

}

?>
