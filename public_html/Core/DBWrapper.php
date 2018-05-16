<?php
/**
 * Created by PhpStorm.
 * User: Weerly
 * Date: 04.05.2018
 * Time: 0:11
 */

namespace Core;


class DBWrapper
{
    protected $file_db;

    public function __construct($db_name)
    {
        $this->file_db = new \SQLite3('databases/testAppDB.sqlite3', SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    }

    public function getDBFile() {
        return $this->file_db;
    }

    public function insertData($query, $statement)
    {

    }
}