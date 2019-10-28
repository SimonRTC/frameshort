<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $request = Request::createFromGlobals();
        return $request->getClientIp();
    }

    private function ThisFieldIsThisUsed(string $table, string $fields, array $datas): ?array {
        $callback   = [];
        $fields     = explode('|', $fields);
        $db         = $this->db;
        $request    = $db()->query("SELECT * FROM `$table`");
        $responses  = $request->fetchAll();
        foreach ($responses as $response) {
            foreach ($fields as $i=>$field) {
                if (isset($response[$field]) && !empty($response[$field]) && $response[$field] == $datas[$i]) {
                    $callback = array_merge($callback, [ "$field" => true ]);
                }
            }
        }
        return $callback;
    }
    
    public function DeleteSession(int $session): bool {
        $db         = $this->db;
        $request    = $db()->prepare('DELETE FROM `auth_sessions` WHERE `session_id` =  :session');
        $request    ->execute([ 'session' => $session ]);
        return (!$request? false: true);
    }

    public function GetSessions(int $session = null, bool $lite = true): array {
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

    public function AddClient(array $Client): string {
        if (!empty($Client)) {
            $entrys = [];
            foreach ($Client as $k=>$entry) { $entrys = array_merge($entrys, [ "$k" => htmlspecialchars($entry) ]); }
            if (!empty($entrys['username']) && !empty($entrys['password']) && !empty($entrys['email'])) {
                $AlreadyTaken = $this->ThisFieldIsThisUsed('auth_clients', 'username|email', [ $entrys['username'], $entrys['email'] ]);
                if (empty($AlreadyTaken)) {
                    $db = $this->db;
                    $request = $db()->prepare('INSERT INTO `auth_clients` (`id`, `username`, `password`, `email`) VALUES (NULL, :username, :password, :email)');
                    $request = $request->execute([
                        'username'  => $entrys['username'],
                        'password'  => hash('sha256', $entrys['password']),
                        'email'     => $entrys['email']
                    ]);
                    if ($request) {
                        return $entrys['username'];
                    }
                } else {
                    return 'ALREADY_TAKEN';
                }
            }
        }
        return 'REGISTER_FAILED';
    }

    public function GetUserByUsername(array $POST, bool $pswd = true): array {
        $callback   = false;
        $username   = (!empty($POST['username']) ? htmlspecialchars($POST['username']): false);
        $password   = (!empty($POST['password']) ? hash('sha256', htmlspecialchars($POST['password'])): false);
        $id         = (!empty($POST['id']) ? $POST['id']: false);

        if ($username && $password || $username && !$pswd || $id) {
            $db         = $this->db;
            $clients    = $db()->prepare('SELECT * FROM `auth_clients` WHERE ' . (!$id? 'username = :username OR email = :email': 'id = :id'));
            $clients->execute((!$id? [ 'username' => $username, 'email' => $username ]: [ 'id' => $id ]));
            if ($clients) {
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
            } else {
                $callback = false;
            }
        }
        return (!$callback ? []: $callback);
    }

    public function SetAuthUser(array $user): ?array {
        $user['banned'] = ($user['banned'] == 0 ? false: true);
        if (!$user['banned']) {
            $session = $this->CreateSessionId();
            $this->AddSessionToDatabase($session, $user);
            $this->AddSessionToClient($session);
        } else {
            $user = null;
        }
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