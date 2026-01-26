<?php
include "../backup.php";

if (runBackup()) {
    echo "Backup OK!";
} else {
    echo "Backup FAILED!";
}
?>
