<?php

function writeBackupLog($message)
{
    $logFile = __DIR__ . "/backup_log.txt";
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

function runBackup()
{
    $sourceDB = __DIR__ . "/data/data.db";
    $googleDriveBackup = "C:/Users/User/My Drive/Farmercoop_backup_database/";
    $externalBackup    = "C:/Users/User/Desktop/Farmercoop_backup_database/";

   
    if (!file_exists($googleDriveBackup)) mkdir($googleDriveBackup, 0777, true);
    if (!file_exists($externalBackup)) mkdir($externalBackup, 0777, true);

 
    $tempCopy = __DIR__ . "/temp_backup.sqlite";
    if (!copy($sourceDB, $tempCopy)) {
        writeBackupLog("ERROR: Could not copy source DB.");
        return false;
    }

    $filename = "backup_" . date("Y-m-d_H-i-s") . ".sqlite";

    copy($tempCopy, $googleDriveBackup . $filename);
    copy($tempCopy, $externalBackup . $filename);

  
    unlink($tempCopy);

    writeBackupLog("Backup created: $filename");
    return true;
}

function autoBackupDaily()
{
    $file = __DIR__ . "/last_backup.txt";
    $today = date("Y-m-d");

    if (!file_exists($file)) {
        file_put_contents($file, $today);
        return runBackup();
    }

   
    $lastBackup = trim(file_get_contents($file));

    
    if ($lastBackup == $today) {
        return false;
    }

   
    file_put_contents($file, $today);
    return runBackup();
}

?>
