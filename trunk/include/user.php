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
class User {
    private $id;
    private $username;
    private $password;
    private $name;
    private $surname;
    private $email;
    private $office;
    private $telephone;
    private $status;
    private $id_group;
    private $group_name;

    function __construct($id, $username, $password, $name, $surname, $email, $office, $telephone, $status, $id_group, $group_name) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->office = $office;
        $this->telephone = $telephone;
        $this->status = $status;
        $this->id_group = $id_group;
        $this->group_name = $group_name;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_username() {
        return $this->username;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_surname() {
        return $this->surname;
    }

    public function get_email() {
        return $this->email;
    }

    public function get_telephone() {
        return $this->telephone;
    }

    public function get_office() {
        return $this->office;
    }

    public function get_status() {
        return $this->status;
    }

    public function get_id_group() {
        return $this->id_group;
    }

    public function get_group_name() {
        return $this->group_name;
    }

}
?>
