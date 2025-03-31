<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\App;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Display the login form
     *
     * @return void
     */
    public function showLoginForm()
    {
        $view = App::get('view');
        $view->assign('title', 'Login');
        $view->display('auth/login');
    }
    
    /**
     * Handle the login request
     *
     * @return void
     */
    public function login()
    {
        $request = $this->request();
        $response = $this->response();
        
        $username = $request->input('username');
        $password = $request->input('password');
        
        if (empty($username) || empty($password)) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Username and password are required'
            ];
            $response->redirect('/login');
        }
        
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);
        
        if (!$user) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Invalid username or password'
            ];
            $response->redirect('/login');
        }
        
        // Store user in session
        $_SESSION['user'] = $user;
        
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'You have been logged in successfully'
        ];
        
        $response->redirect('/');
    }
    
    /**
     * Handle the logout request
     *
     * @return void
     */
    public function logout()
    {
        // Clear the session
        session_unset();
        session_destroy();
        
        // Start a new session for flash message
        session_start();
        
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'You have been logged out successfully'
        ];
        
        $this->response()->redirect('/');
    }
}