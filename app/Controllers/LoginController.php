<?php

namespace App\Controllers;

use Web\Framework\Authentication\SessionAuthInterface;
use Web\Framework\Controller\AbstractController;
use Web\Framework\Http\RedirectResponse;
use Web\Framework\Http\Response;

class LoginController extends AbstractController
{

    public function __construct(
        private readonly SessionAuthInterface $auth
    )
    {
    }

    public function form(): Response
    {
        return $this->render('login.html.twig');
    }

    public function login(): RedirectResponse
    {
        $isAuth = $this->auth->authenticate(
            $this->request->input('email'),
            $this->request->input('password'),
        );

        if(!$isAuth){
            $this->request->getSession()->setFlash('error', 'Incorrect login or password');
            return new RedirectResponse('/login');
        }
        $this->request->getSession()->setFlash('success', 'Login success');
        return new RedirectResponse('/dashboard');
    }

    public function logout(): Response
    {
        $this->auth->logout();
        return new RedirectResponse('/login');
    }
}