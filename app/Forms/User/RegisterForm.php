<?php

namespace App\Forms\User;

use App\Entities\User;
use App\Services\UserService;

class RegisterForm
{
    private ?string $name;
    private string $email;
    private string $password;
    private string $passwordConfirmation;

    public function __construct(
        private UserService $userService,
    )
    {
    }

    public function setFields(string $email, string $password, string $passwordConfirmation,?string $name = null): void
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }
    public function save(): User
    {
        $user = User::create(
            $this->email,
            password_hash($this->password, PASSWORD_DEFAULT),
            new \DateTimeImmutable(),
            $this->name,
        );
        $user = $this->userService->save($user);

        return  $user;
    }
    public function getValidationErrors(): array
    {
        $errors = [];
        if(!empty($this->name) && strlen($this->name) > 64){
            $errors[] = 'Maximum name length is 50 characters';
        }
        if(empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            $errors[] = 'Invalid email format';
        }
        if(empty($this->password) || strlen($this->password) < 8){
            $errors[] = 'Minimum password length is 8 characters';
        }
        if($this->password !== $this->passwordConfirmation){
            $errors[] = 'Password mismatch';
        }
        return $errors;
    }

    public function hasValidationErrors(): bool
    {
        return !empty($this->getValidationErrors());
    }
}