<?php
session_start();
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include '../include/db_connection.php';
include '../include/db_utils.php';

$connection = db_get_connection();
$resultsets = get_resultsets();
?>
<form action="manage_resultset.php" method="POST">
    <table border="0">
        <tr>
            <td><select name="resultset_id">
        <?php foreach ($resultsets as $resultset) {
            $id = $resultset->get_id();
            $name = $resultset->get_alias();
            ?>
        <option value="<?php echo $id ?>">
                <?php echo $name; ?>
        </option>
        <?php } ?>
    </select></td>
            <td><input type="submit" value="Submit" /></td>
        </tr>
    </table>
    <input type="hidden" name="action" value="delete" />
    
</form>