<?php

namespace App;

class Pusher {

    public $Notification;

    public function __construct() {
        $this->Notification = null;
    }

    public function IsNotificationInStandBy(): bool {
        return (!empty($this->Notification)? true: false);
    }

    public function GetMessageText(string $code = null): string {
        (empty($code)? $code = $this->Notification: null);
        $parse      = $this->GetPusherParse();
        $title      = null;
        foreach ($parse as $key=>$m) {
            if ($code == $key) {
                $title = $m['message'];
            }
        }
        return $title; 
    }

    public function GetType(string $code = null): string {
        (empty($code)? $code = $this->Notification: null);
        $parse      = $this->GetPusherParse();
        $title      = null;
        foreach ($parse as $key=>$m) {
            if ($code == $key) {
                $title = $m['type'];
            }
        }
        return $title; 
    }

    public function GetTypeLabel(string $code = null): string {
        (empty($code)? $code = $this->Notification: null);
        $parse      = $this->GetPusherParse();
        $message    = null;
        foreach ($parse as $key=>$m) {
            if ($code == $key) {
                $message = $m['label'];
            }
        }
        return $message;
    }

    public function SetNotification(string $code) {
        $this->Notification = $code;
    }

    private function GetPusherParse(): ?array {
        $parse      = file_get_contents( realpath(__DIR__ . '/..') . '/config/push.json' );
        $parse      = json_decode($parse, true);
        return $parse;
    }
}

?>