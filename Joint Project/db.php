<?php

$local = "localhost";
$user = "root";
$pass = "";

$dbconnect = new mysqli($local, $user, $pass);
 
if($dbconnect === false){
    die("ERROR: Could not dbconnect. " . mysqli_connect_error());
}

$sql = 'CREATE DATABASE IF NOT EXISTS patient;';
if (!$dbconnect->query($sql) === TRUE) {
die('Error creating database: ' . $dbconnect->error);
}
$sql = 'USE patient;';
if (!$dbconnect->query($sql) === TRUE) {
die('Error using database: ' . $dbconnect->error);
}

 
$sql = 'CREATE TABLE IF NOT EXISTS patient (
    id int NOT NULL AUTO_INCREMENT,
    first_name varchar(256) NOT NULL,
    last_name varchar(256) NOT NULL,
    pass varchar(256) NOT NULL,
    email_address varchar(256) NOT NULL,
    home_address varchar(256) NOT NULL,
    pps_no varchar(256) NOT NULL,
    phone_no varchar(256) NOT NULL,
    vac_state varchar(256) NOT NULL,
    test_state varchar(256) NOT NULL,
    PRIMARY KEY (id));';
    if (!$dbconnect->query($sql) === TRUE) {
    die('Error creating table: ' . $dbconnect->error);
    }
    $sql = 'CREATE TABLE IF NOT EXISTS encrypttable (
pid int NOT NULL AUTO_INCREMENT,
randkey varchar(256) NOT NULL,
iv varchar(256) NOT NULL,
pps_no varchar(256) NOT NULL,
PRIMARY KEY (pid));';
if (!$dbconnect->query($sql) === TRUE) {
die('Error creating table: ' . $dbconnect->error);
}
$sql = 'CREATE TABLE IF NOT EXISTS appointment (
    apId int NOT NULL AUTO_INCREMENT,
    testCenter varchar(256) NOT NULL,
    apDate varchar(256) NOT NULL,
    apTime varchar(256) NOT NULL,
    apType varchar(256) NOT NULL,
    pps_no varchar(256) NOT NULL,
    PRIMARY KEY (apId));';
    if (!$dbconnect->query($sql) === TRUE) {
    die('Error creating table: ' . $dbconnect->error);
    }
$sql = 'CREATE TABLE IF NOT EXISTS doctor (
adId int NOT NULL AUTO_INCREMENT,
user varchar(256) NOT NULL,
pass varchar(256) NOT NULL,
randkey varchar(256) NOT NULL,
iv varchar(256) NOT NULL,
PRIMARY KEY (adId));';
if (!$dbconnect->query($sql) === TRUE) {
die('Error creating table: ' . $dbconnect->error);
}
?>