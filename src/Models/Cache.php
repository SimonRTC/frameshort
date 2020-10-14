<?php

namespace Frameshort;

class Cache {

    private $Namespace;
    private $Encrypt;
    
    public function __construct(string $namespace, bool $encrypt = false) {
        $this->Namespace    = $namespace;
        $this->Encrypt      = $encrypt;
        $this->Path         = __PATH__ . "/tmp/cache/{$this->Namespace}/";
        if (!realpath($this->Path)) {
            mkdir($this->Path);
        }
        $this->Path         = realpath($this->Path);
    }

    /**
     * Get cached datas
     * 
     * @param string Name
     * @return string Cached datas
     */
    public function GetDatas(string $name): array {
        $Path = "{$this->Path}/" . hash("sha256", $name);
        $Path = realpath($Path);
        if ($Path !== false) {
            $value      = file_get_contents($Path);
            $value      = json_decode($value, false);
            $metadatas  = $value->metadatas;
            $value      = (!empty($value->value)? $value->value: null);
        }
        return [ (!empty($value)? ($this->Encrypt? $this->DecryptData($value): $value): null), (!empty($metadatas)? $metadatas: []) ];
    }

    /**
     * Set data in cache
     * 
     * @param string Name
     * @param string Value
     * @return void
     */
    public function SetDatas(string $name, string $value, array $metadatas = []): void {
        $Path = "{$this->Path}/" . hash("sha256", $name);
        $value = ($this->Encrypt? $this->EncryptData($value): $value);
        file_put_contents($Path, json_encode([
            "name"      => $name,
            "value"     => $value,
            "metadatas" => $metadatas,
            "timestamp" => \time(),
        ]));
        return;
    }

    /**
     * Encrypt datas
     * 
     * @param string Decrypted value
     * @return string Encrypted value
     */
    private function EncryptData(string $value): string {
        return $value;
    }

    /**
     * Decrypt datas
     * 
     * @param string Encrypted value
     * @return string Decrypted value
     */
    private function DecryptData(string $value): string {
        return $value;
    }

}

?>