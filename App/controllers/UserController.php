<?php

namespace App\controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

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
        }

        //Check if email exists
        $params = [
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM  users where email = :email', $params)->fetch();

        if ($user) {
            $errors['email'] = 'That email already exists';
            loadView('users/create', [
                'errors' => $errors
            ]);
            exit;
        }

        //Create user account
        $params = [
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $this->db->query('INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)', $params);

        //GET new user id
        $userId = $this->db->conn->lastInsertId();

        //SET user session
        Session::set('user', [
            'id' =>  $userId,
            'name' =>  $name,
            'email' =>  $email,
            'city' =>  $city,
            'state' =>  $state,
        ]);

        inspectAndDie(Session::get('user'));

        redirct('/');
    }

    /**
     * Log out user and kill session
     * 
     * @return void
     */
    public function logout()
    {
        Session::clearAll('user');

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

        redirct('/');
    }

    /**
     * Authenticate user with email and password
     * 
     * @return void
     */
    public function authenticate()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $errors = [];

        //Validate the email
        if (!Validation::email($email)) {
            $errors['email'] = 'Please enter valid email.';
        }

        //Validate the password
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        //Check for errrors
        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        //check for email
        $params = [
            'email' => $email
        ];

        $user = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

        if (!$user) {
            $errors['email'] = 'Incorrect Credentials';
        }

        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        //Check is password is correct
        if (!password_verify($password, $user->password)) {
            $errors['email'] = 'Incorrect Credentials';
            loadView('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        //SEt user session
        Session::set('user', [
            'id' =>  $user->id,
            'name' =>  $user->name,
            'email' =>  $user->email,
            'city' =>  $user->city,
            'state' =>  $user->state,
        ]);

        redirct('/');
    }
}
