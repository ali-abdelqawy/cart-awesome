<?php 

namespace App\Controllers;

use App\Models\User;
use App\Models\FormProcessing\FormValidator;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function authenticate()
    {
        if ($this->areFieldsFilled() && ! $this->arePasswordsMatched()) {
            return view('login', ['error' => 'incorrect email or password']);
        }

        $this->request->request->add([
            'id' => $this->getUserData()['id']
        ]);
        
        session_start();

        $_SESSION['user'] = $this->request->request;

        return RolesController::goToUserProfile($this->getUserData()['role_fk']);
    }
    
    public function logout()
    {
        killSession();

        return redirect('');
    }
    private function areFieldsFilled()
    {
        return ( new ValidationController($this->request, (new FormValidator($this->request->request)), 'login'))->checkEmptyFields('login') ? 0 : 1;
    }

    private function arePasswordsMatched()
    {
        return (
            ! is_null($this->getUserData()['password']) &&
            password_verify($this->request->request->get('password'), $this->getUserData()['password'])
        ) ? 1 : 0;
    }
    
    private function getUserData()
    {
        return (new User)->get($this->request->request->get('email'));
    }
}