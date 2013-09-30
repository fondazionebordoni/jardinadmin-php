<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<form action="../manage_resultset.php" method="POST">
    <table border="0">
            <tr>
                <td>name:</td>
                <td><input type="text" name="resultset_name" /></td>
            </tr>
            <tr>
                <td>alias:</td>
                <td<input type="text" name="resultset_alias" /></td>
            </tr>
            <tr>
                <td>statement:</td>
                <td><input type="text" name="resultset_statement" /></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="Submit" /></td>
            </tr>
    </table>
    <input type="hidden" name="action" value="new" />
</form>