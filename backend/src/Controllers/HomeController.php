<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\App;

class HomeController extends Controller
{
    /**
     * Display the home page
     *
     * @return void
     */
    public function index()
    {
        $view = App::get('view');
        $db = $this->db();
        
        // Get latest mods
        $latestMods = $db->query(
            "SELECT m.*, u.username 
             FROM mod m 
             JOIN user u ON m.userid = u.userid 
             ORDER BY m.created DESC 
             LIMIT 10"
        )->fetchAll();
        
        // Get trending mods
        $trendingMods = $db->query(
            "SELECT m.*, u.username 
             FROM mod m 
             JOIN user u ON m.userid = u.userid 
             ORDER BY m.downloads DESC 
             LIMIT 10"
        )->fetchAll();
        
        $view->assign('latestMods', $latestMods);
        $view->assign('trendingMods', $trendingMods);
        $view->assign('title', 'Home');
        
        $view->display('home');
    }
}