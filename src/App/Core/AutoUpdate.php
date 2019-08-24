<?php

/**
 * 
 * UNTESTED
 * 
 */

namespace App\Core;

class AutoUpdate {

    private $CurrentVersion;
    private $GlobalAppUrl;

    public function __construct() {
        $this->CurrentVersion   = '1.0.0';
        $this->GlobalAppUrl     = 'http://localhost:8000/';
    }

    public function Update() {

        $update = new \VisualAppeal\AutoUpdate(__DIR__ . '/temp', __DIR__, 60);
        $update->setCurrentVersion($this->CurrentVersion);
        $update->setUpdateUrl($this->GlobalAppUrl);

        // $update->addLogHandler(new Monolog\Handler\StreamHandler(__DIR__ . '/update.log'));
        // $update->setCache(new Desarrolla2\Cache\Adapter\File(__DIR__ . '/cache'), 3600);

        if ($update->checkUpdate() === false) { die('Could not check for updates! See log file for details.'); }
        if ($update->newVersionAvailable()) {
            echo 'New Version: ' . $update->getLatestVersion();
            $simulate = true;
            $result = $update->update($simulate);
            if ($result === true) {
                echo 'Update simulation successful<br>';
            } else {
                echo 'Update simulation failed: ' . $result . '!<br>';
            }
        } else {
            echo 'Your application is up to date';
        }

    }

}

?>