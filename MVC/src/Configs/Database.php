<?php

class Database {
    private static ?PDO $instance = null;

    private const HOST     = 'srv-cesi-ton-job-grp1.database.windows.net';
    private const DB       = 'db-cesi-ton-job';
    private const USER     = 'adminCesiTonJob';
    private const PASSWORD = 'CesiTonJob.admin1';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = 'sqlsrv:server=' . self::HOST . ',1433;Database=' . self::DB . ';Encrypt=yes;TrustServerCertificate=no';
            
            self::$instance = new PDO($dsn, self::USER, self::PASSWORD, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$instance;
    }
}