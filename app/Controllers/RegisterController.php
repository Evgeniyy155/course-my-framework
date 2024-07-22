<?php

namespace App\Controllers;

use App\Forms\User\RegisterForm;
use App\Services\UserService;
use Web\Framework\Authentication\SessionAuthInterface;
use Web\Framework\Controller\AbstractController;
use Web\Framework\Http\RedirectResponce;
use Web\Framework\Http\Response;

class RegisterController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private SessionAuthInterface $auth,
    )
    {
    }

    public function form(): Response
    {
        return $this->render('register.html.twig');
    }

    public function register()
    {
        // 1. Створити модель форми
        $form = new RegisterForm($this->userService);
        $form->setFields(
            $this->request->input('email'),
            $this->request->input('password'),
            $this->request->input('password_confirmation'),
            $this->request->input('name')
        );
        // 2. Валідація
        // Якщо є помилки валідаціїї, добавить в сесію і перенаправити на форму
        if($form->hasValidationErrors()){
            foreach ($form->getValidationErrors() as $error){
                $this->request->getSession()->setFlash('error', $error);
            }
            return new RedirectResponce('/register');
        }
        // 3. Зареєструвати користувача визвав $form->save()
        $user = $form->save();
        // 4. Добавить повідомлення про успішну реєстрацію
        $this->request->getSession()->setFlash('success', "User {$user->getEmail()} has registered successfully");
        // 5. Війти в систему під користувачем
        $this->auth->login($user);
        // 6. Перенаправити на потрібну сторніку
        return new RedirectResponce('/register');
    }
}