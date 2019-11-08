<?php

namespace App;

class Pusher {

    public $Notification;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        $this->Notification = null;
    }

    /**
     * IsNotificationInStandBy
     *
     * @return bool
     */
    public function IsNotificationInStandBy(): bool {
        return (!empty($this->Notification)? true: false);
    }

    /**
     * GetMessageText
     *
     * @param  string $code
     *
     * @return string
     */
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

    /**
     * GetType
     *
     * @param  string $code
     *
     * @return string
     */
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

    /**
     * GetTypeLabel
     *
     * @param  string $code
     *
     * @return string
     */
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

    /**
     * SetNotification
     *
     * @param  string $code
     *
     * @return void
     */
    public function SetNotification(string $code) {
        $this->Notification = $code;
    }

    /**
     * GetPusherParse
     *
     * @return array
     */
    private function GetPusherParse(): ?array {
        $parse      = file_get_contents( realpath(__DIR__ . '/..') . '/config/push.json' );
        $parse      = json_decode($parse, true);
        return $parse;
    }
}

?>