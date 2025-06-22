<?php
/**
 * Database Configuration for Robert CAT Tool
 * This file contains database connection settings
 */

return [
    'host' => 'localhost',
    'dbname' => 'robert_cat_tool',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
]; 