<?php

namespace App\Core;

class Auth {

    public $ClientAuth;

    public function __construct(\App\Databases $Databases) {
        $this->db           = function() use ($Databases) { return $Databases->PDO($Databases->databases['website']); };
        $this->ClientAuth   = [];
    }

    private function DatabaseEncoding(string $string, bool $send = true): string {
        return ($send ? (base64_encode($string)): (base64_decode($string)));
    }

    private function GetCurrentClientIp():string {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
         
        return $ipaddress;
    }

    private function DeleteSession(int $session): bool {
        $db         = $this->db;
        $request    = $db()->prepare('DELETE FROM `auth_sessions` WHERE `session_id` =  :session');
        $request    ->execute([ 'session' => $session ]);
        return (!$request? false: true);
    }

    private function GetSessions(int $session = null, bool $lite = true): array {
        $db         = $this->db;
        $sessions   = $db()->query('SELECT * FROM `auth_sessions`');
        $browse     = true;
        $opened     = [];
        while ($data = $sessions->fetch()) {
            if (empty($session) || $session == $data['session_id'])
            array_push($opened, ($lite? $data['session_id']: [
                'session_id'    => $data['session_id'],
                'client_ip'     => $this->DatabaseEncoding($data['client_ip'], false),
                'client'        => $this->DatabaseEncoding($data['client'], false),
                'expiry'        => new \DateTime($data['expiry'])
            ]));
        }
        return $opened;
    }

    private function GenerateRandomId(): int {
        $id     = rand(1024, 65536);
        $id     .= rand(($id*6), ($id*16));
        $id     .= rand(($id*6), ($id*16));
        return ((int)$id);
    }

    private function CreateSessionId(): int {
        $browse     = true;
        $opened     = $this->GetSessions();
        while ($browse) {
            $random         = $this->GenerateRandomId();
            $AlreadyUsed    = false;
            foreach ($opened as $session) {
                if ($session == $random) {
                    $AlreadyUsed = true;
                    break;
                }
            }
            (!$AlreadyUsed ? $browse = false: $browse = true);
        }
        return $random;
    }

    private function AddSessionToDatabase(int $session, array $user) {
        $db         = $this->db;
        $expiry     = ((new \DateTime(date('Y-m-d H:i:s')))->add(new \DateInterval('P1D')));
        $expiry     = $expiry->format('Y-m-d H:i:s');
        $request    = $db()->prepare('INSERT INTO `auth_sessions` (`session_id`, `client_ip`, `client`, `expiry`) VALUES (:session_id, :client_ip, :client, :expiry)');
        $response   = $request->execute([
            'session_id'    => $session,
            'client_ip'     => $this->DatabaseEncoding($this->GetCurrentClientIp(), true),
            'client'        => $this->DatabaseEncoding($user['id'], true),
            'expiry'        => $expiry
        ]);
    }

    private function AddSessionToClient(int $session): bool {
        $success = false;
        if (setcookie("SESSION", $session, time() + (86400 * 30), "/")) {
            $success = true;
        }
        return $success;
    }

    public function GetUserByUsername(array $POST, bool $pswd = true): array {
        $callback   = false;
        $username   = (!empty($POST['username']) ? htmlspecialchars($POST['username']): false);
        $password   = (!empty($POST['password']) ? hash('sha256', htmlspecialchars($POST['password'])): false);
        $id         = (!empty($POST['id']) ? $POST['id']: false);

        if ($username && $password || $id) {
            $db         = $this->db;
            $clients    = $db()->query('SELECT * FROM `auth_clients`');
            while ($data = $clients->fetch()) {
                if ($data['username'] == $username || $data['email'] == $username || $data['id'] == $id) {
                    $user = $data;
                    break;
                }
            }
            if (!empty($user) && $user) {
                if ($data['password'] == $password || $pswd == false) {
                    $callback = $user;
                }   
            }
        }

        return (!$callback ? []: $callback);
    }

    public function SetAuthUser(array $user): array {
        $session = $this->CreateSessionId();
        $this->AddSessionToDatabase($session, $user);
        $this->AddSessionToClient($session);
        return $user;
    }

    public function GetClient(int $session): array {
        $id         = $this->GetSessions($session, false);
        $id         = (!empty($id) ? $id = $id[0]: $id = false);
        $session_id = (int)$id['session_id'];
        if ($session == $session_id) {
            if ($id['client_ip'] == 'UNKNOWN' || $id['client_ip'] == $this->GetCurrentClientIp()) {
                $client = $this->GetUserByUsername([ 'id' => ((int)$id['client']) ], false);
            }
        }
        $cb = (!empty($client)? $client: []);
        $this->ClientAuth = $cb;
        return $cb;
    }

    public function Logout(int $session): bool {
        $success = false;
        if (setcookie('SESSION', null, -1, '/')) {
            $this->DeleteSession($session);
            $success = true;
        }
        return $success;
    }

}

?>