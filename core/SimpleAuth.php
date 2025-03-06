<?php

namespace Puf\Core;

class SimpleAuth
{
    private $usersFile;
    private $users;
    private $cookieName;
    private $cookieOptions;

    public function __construct($config)
    {
        if (!isset($config['storage_directory'], $config['session_cookie_name'], $config['security'])) {
            throw new \Exception("Missing required config keys for SimpleAuth.");
        }

        // Users file path
        $this->usersFile = realpath(__DIR__ . '/../../../' . $config['storage_directory'] . '/users.json');
        $this->cookieName = $config['session_cookie_name'];
        $this->cookieOptions = $config['security'];

        if (!$this->usersFile || !file_exists($this->usersFile)) {
            throw new \Exception("Users file not found at: " . $config['storage_directory'] . '/users.json');
        }

        $this->users = json_decode(file_get_contents($this->usersFile), true) ?: [];
    }

    public function isLoggedIn()
    {
        return isset($_COOKIE[$this->cookieName]) && $_COOKIE[$this->cookieName] === 'true';
    }

    public function login($username, $password)
    {
        if (!isset($this->users[$username])) {
            return false;
        }

        if (password_verify($password, $this->users[$username]['password'])) {
            setcookie(
                $this->cookieName,
                'true',
                time() + 3600,
                '/',
                '',
                $this->cookieOptions['cookie_secure'],
                $this->cookieOptions['cookie_httponly']
            );
            return true;
        }

        return false;
    }

    public function logout()
    {
        setcookie($this->cookieName, '', time() - 3600, '/', '', $this->cookieOptions['cookie_secure'], $this->cookieOptions['cookie_httponly']);
    }

    public function getUser()
    {
        return $this->isLoggedIn() ? current($this->users) : null;
    }
}
