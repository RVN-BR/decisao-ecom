<?php

class DB extends mysqli {

    private static $instance;
    private static $host = "localhost";
    private static $port = "3306";
    private static $user = "ufabc";
    private static $pass = "|&FwN931%75T/;x";
    private static $database = "sadcce";

    public static function singleton() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    function abrirBD() {

        $this->mysqli(self::$host, self::$user, self::$pass, self::$database, self::$port);
    }

    function __construct() {

    }

    function __destruct() {
        $this->close();
    }

    function __clone() {
        trigger_error('Classe Singleton, nao permite clonagem.', E_USER_ERROR);
    }

}