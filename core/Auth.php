<?php

namespace Puf\Core;

class Auth
{
    private $users = [];

    public function __construct()
    {
        $this->loadUsers();
    }

    private function loadUsers()
    {
        $usersFile = 'users.json';
        if (file_exists($usersFile)) {
            $this->users = json_decode(file_get_contents($usersFile), true);
        }
    }

    public function generateMagicLink($email)
    {
        $token = bin2hex(random_bytes(16));
        $this->users[$email]['token'] = $token;
        $this->saveUsers();
        return $token;
    }

    public function login($token)
    {
        foreach ($this->users as $email => $user) {
            if (isset($user['token']) && $user['token'] === $token) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user'] = $email;
                unset($this->users[$email]['token']);
                $this->saveUsers();
                return true;
            }
        }
        return false;
    }

    public function logout()
    {
        unset($_SESSION['logged_in']);
        unset($_SESSION['user']);
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    }

    public function getUser()
    {
        return $_SESSION['user'];
    }

    private function saveUsers()
    {
        $usersFile = 'users.json';
        file_put_contents($usersFile, json_encode($this->users));
    }

    public function addUser($email)
    {
        if (!isset($this->users[$email])) {
            $this->users[$email] = ['token' => null];
            $this->saveUsers();
        }
    }

}
