<?php

namespace App\controllers;

use Framework\Database;
use Framework\Validation;

class UserController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show login page
     * @return void 
     *
     */
    public function login()
    {
        loadView('users/login');
    }

    /**
     * Show register page
     * @return void 
     *
     */
    public function create()
    {
        loadView('users/create');
    }

    /**
     * Store user in DB
     * 
     * @return void
     */
    public function store()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];


        $errors = [];

        //Validate the email
        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter valid email.';
        }

        //Validate the name
        if (!Validation::string($name, 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 chars.';
        }

        //Validate the password
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        //Validate the password match
        if (!Validation::match($password, $passwordConfirmation)) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state,

                ],
            ]);
            exit;
        } else {
            inspectAndDie('Store');
        }
    }
}
