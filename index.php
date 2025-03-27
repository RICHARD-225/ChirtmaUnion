<?php
require("includes/config.php");

if (isset($_GET['message']) && $_GET['message'] == 'deconnexion') {
    echo "<p>Vous avez été déconnecté avec succès.</p>";
}
?>
