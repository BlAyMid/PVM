<?php

namespace App\dto;

readonly class UserDTO {
    public string $name;
    public string $email;
    public int $password_hash;

    public function __construct($name, $email, $password_hash) {
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = $password_hash;
    }
}

