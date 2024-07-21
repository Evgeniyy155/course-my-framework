<?php

namespace App\Controllers;

use Web\Framework\Authentication\SessionAuthInterface;
use Web\Framework\Controller\AbstractController;
use Web\Framework\Http\RedirectResponce;
use Web\Framework\Http\Response;

class LoginController extends AbstractController
{

    public function __construct(
        private SessionAuthInterface $sessionAuth
    )
    {
    }

    public function form(): Response
    {
        return $this->render('login.html.twig');
    }

    public function login(): RedirectResponce
    {
        $isAuth = $this->sessionAuth->authenticate(
            $this->request->input('email'),
            $this->request->input('password'),
        );

        if(!$isAuth){
            $this->request->getSession()->setFlash('error', 'Incorrect login or password');
            return new RedirectResponce('/login');
        }
        $this->request->getSession()->setFlash('success', 'Login success');
        return new RedirectResponce('/dashboard');
    }
}