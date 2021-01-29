<?php

/**
 * MySQL Databases Daily Backup
 * 
 * @author  Gustavo Novaro <gnovaro@gmail.com>
 * @version <git_id>
 * 
 * Usage
 * php backup_db.php
 * 
 * Crontab Example: (Run every day at 12 am)
 * 0 0 * * * cd /var/www/cron/ && php backup_db.php
 */
const ERROR_DATABASE_CONNECT = 1;
const ERROR_CREATE_DESTINATION = 2;

require_once 'config.php';

$mode = 0760;
$path = getcwd();
$year = date('Y'); 
$month = date('m');
$day = date('d');
$destination_path = $path.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.$year.DIRECTORY_SEPARATOR.$month.DIRECTORY_SEPARATOR.$day;

$db_exceptions = [
    'mysql',
    'information_schema',
    'performance_schema',
    'test'
];
$debug = false;

// Establish connection to the database
try {
    $con = mysqli_connect($config['hostname'], $config['user'], $config['password']);
} catch (\Throwable $th) {
    echo $th->getMessage();
    exit(ERROR_DATABASE_CONNECT);
    //throw $th;
}

// Create backup destionation folder
try {
    mkdir($destination_path, $mode, true);
} catch (\Throwable $th) {
    //throw $th;
    echo $th->getMessage();
    exit(ERROR_CREATE_DESTINATION);
}

//Seteo la conexion en utf
mysqli_query($con, "SET NAMES 'utf8';");

$rs = mysqli_query($con, 'SHOW databases');

while ($row = mysqli_fetch_assoc($rs)) {
    if (!in_array($row['Database'], $db_exceptions)) {
        $db = $row['Database'];
        $file = $db . '-' . date('Y-m-d') . '.sql';

        $dump_command = "mysqldump --defaults-file=mysql.conf --routines $db > $destination_path/$file";
        if ($debug) {
            echo "$dump_command\n";
        }
        exec($dump_command);
        exec("gzip -c $destination_path/$file > $destination_path/$file" . '.gz');

        //Borro el original sin comprimir
        unlink("$destination_path/$file");
    } //if
}//foreach
