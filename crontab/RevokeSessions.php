<?php

    require realpath(__DIR__ . '/..') . '/src/autoload.php';

    $auth           = new \App\Core\Auth((new \App\Databases));
    $sessions       = $auth->GetSessions(null, false);
    $CurrentDate    = strtotime(date('Y-m-d H:i:s'));

    foreach ($sessions as $session) {
        if ($CurrentDate > strtotime($session['expiry']->format('Y-m-d H:i:s'))) {
            $auth->DeleteSession(((int)$session['session_id']));
        }
    }

?>