<?php

namespace Web\Framework\Authentication;

use Web\Framework\Session\Session;
use Web\Framework\Session\SessionInterface;

class SessionAuthentication implements SessionAuthInterface
{
    private AuthUserInterface $user;
    public function __construct(
        private UserServiceInterface $userService,
        private SessionInterface $session,
    )
    {
    }

    public function authenticate(string $email, string $password): bool
    {
        $user = $this->userService->findByEmail($email);
        if(!$user){
            return false;
        }
        if(password_verify($password, $user->getPassword())){
            $this->login($user);
            return true;
        }

        return false;
    }

    public function login(AuthUserInterface $user): void
    {
        $this->session->set(Session::AUTH_KEY, $user->getId());
        $this->user = $user;
    }

    public function logout(): void
    {
        $this->session->remove(Session::AUTH_KEY);
    }

    public function getUser(): AuthUserInterface
    {
        return $this->user;
    }

    public function check(): bool
    {
        return $this->session->has(Session::AUTH_KEY);
    }
}