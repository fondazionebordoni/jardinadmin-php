<?php
/**
 * Description of resource
 *
 * @author gpantanetti
 */
class Resource {

    private $id;
    private $name;
    private $alias;
    private $type;
    private $def;
    private $header;
    private $search;
    private $grouping;

    function __construct($id, $name, $alias, $type = null, $def = null, $header = null, $search = null, $grouping = null) {
        $this->id = $id;
        $this->name = $name;
        $this->alias = $alias;
        $this->type = $type;
        $this->def = $def;
        $this->header = $header;
        $this->search = $search;
        $this->grouping = $grouping;
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

    public function get_type() {
        return $this->type;
    }

    public function get_def() {
        return $this->def;
    }

    public function get_header() {
        return $this->header;
    }

    public function get_search() {
        return $this->search;
    }

    public function get_grouping() {
        return $this->grouping;
    }

}

?>
