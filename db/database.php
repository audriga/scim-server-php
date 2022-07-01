<?php

$database = new SQLite3('db/scim-mock.sqlite');

// Reset
// $database->exec("DROP TABLE users");
// $database->exec("DROP TABLE groups");

$user_db_sql = "CREATE TABLE IF NOT EXISTS users (
    id varchar(160) NOT NULL UNIQUE,
    userName varchar(160) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT 1,
    externalId varchar(160) NULL,
    profileUrl varchar(160) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
)";

$database->exec($user_db_sql);

$group_db_sql = "CREATE TABLE IF NOT EXISTS groups (
    id varchar(160) NOT NULL UNIQUE,
    displayName varchar(160) NOT NULL DEFAULT '',
    members TEXT NOT NULL DEFAULT '',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
)";

$database->exec($group_db_sql);
