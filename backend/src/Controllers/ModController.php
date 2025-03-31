<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\App;
use App\Services\ModService;

class ModController extends Controller
{
    private $modService;
    
    public function __construct()
    {
        parent::__construct();
        $this->modService = new ModService();
    }

    /**
     * Display a list of mods
     *
     * @return void
     */
    public function index()
    {   
        $view = App::get('view');
        $mods = $this->modService->getAllMods();
        
        $view->assign('mods', $mods);
        $view->assign('title', 'Mods');
        $view->display('mod/index');
    }
    
    /**
     * Display a specific mod
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        $view = App::get('view');
        $mod = $this->modService->getMod($id);
        
        if (!$mod) {
            $this->response()->setStatusCode(404);
            $view->assign('message', 'Mod not found');
            $view->assign('title', 'Not Found');
            $view->display('error/404');
            return;
        }
        
        // Get releases for this mod
        $releases = $modModel->getReleases($id);
        
        // Get comments for this mod
        $comments = $modModel->getComments($id);
        
        $view->assign('mod', $mod);
        $view->assign('releases', $releases);
        $view->assign('comments', $comments);
        $view->assign('title', $mod['name']);
        $view->display('mod/show');
    }
    
    /**
     * Display the form to create a new mod
     *
     * @return void
     */
    public function create()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'You must be logged in to create a mod'
            ];
            $this->response()->redirect('/login');
            return;
        }
        
        $view = App::get('view');
        $view->assign('title', 'Create Mod');
        $view->display('mod/create');
    }
    
    /**
     * Store a new mod
     *
     * @return void
     */
    public function store()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->response()->json([
                'status' => 'error',
                'message' => 'You must be logged in to create a mod'
            ], 401);
            return;
        }
        
        $request = $this->request();
        $modModel = new Mod();
        
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'userid' => $_SESSION['user']['userid'],
            'created' => date('Y-m-d H:i:s')
        ];
        
        $modId = $modModel->create($data);
        
        if (!$modId) {
            $this->response()->json([
                'status' => 'error',
                'message' => 'Failed to create mod'
            ], 500);
            return;
        }
        
        $this->response()->json([
            'status' => 'success',
            'message' => 'Mod created successfully',
            'data' => [
                'id' => $modId
            ]
        ]);
    }
    
    /**
     * Display the form to edit a mod
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'You must be logged in to edit a mod'
            ];
            $this->response()->redirect('/login');
            return;
        }
        
        $view = App::get('view');
        $modModel = new Mod();
        
        $mod = $modModel->find($id);
        
        if (!$mod) {
            $this->response()->setStatusCode(404);
            $view->assign('message', 'Mod not found');
            $view->assign('title', 'Not Found');
            $view->display('error/404');
            return;
        }
        
        // Check if user is the owner of the mod or an admin
        if ($mod['userid'] != $_SESSION['user']['userid'] && $_SESSION['user']['rolecode'] != 'admin') {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'You do not have permission to edit this mod'
            ];
            $this->response()->redirect('/mod/' . $id);
            return;
        }
        
        $view->assign('mod', $mod);
        $view->assign('title', 'Edit ' . $mod['name']);
        $view->display('mod/edit');
    }
    
    /**
     * Update a mod
     *
     * @param int $id
     * @return void
     */
    public function update($id)
    {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $this->response()->json([
                'status' => 'error',
                'message' => 'You must be logged in to update a mod'
            ], 401);
            return;
        }
        
        $request = $this->request();
        $modModel = new Mod();
        
        $mod = $modModel->find($id);
        
        if (!$mod) {
            $this->response()->json([
                'status' => 'error',
                'message' => 'Mod not found'
            ], 404);
            return;
        }
        
        // Check if user is the owner of the mod or an admin
        if ($mod['userid'] != $_SESSION['user']['userid'] && $_SESSION['user']['rolecode'] != 'admin') {
            $this->response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to update this mod'
            ], 403);
            return;
        }
        
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description')
        ];
        
        $success = $modModel->update($id, $data);
        
        if (!$success) {
            $this->response()->json([
                'status' => 'error',
                'message' => 'Failed to update mod'
            ], 500);
            return;
        }
        
        $this->response()->json([
            'status' => 'success',
            'message' => 'Mod updated successfully'
        ]);
    }
    
    /**
     * API: Get all mods
     *
     * @return void
     */
    public function apiIndex()
    {
        $modModel = new Mod();
        $mods = $modModel->getAllWithUsers();
        
        $this->response()->json([
            'status' => 'success',
            'data' => $mods
        ]);
    }
    
    /**
     * API: Get a specific mod
     *
     * @param int $id
     * @return void
     */
    public function apiShow($id)
    {
        $modModel = new Mod();
        $mod = $modModel->getWithUser($id);
        
        if (!$mod) {
            $this->response()->json([
                'status' => 'error',
                'message' => 'Mod not found'
            ], 404);
            return;
        }
        
        // Get releases for this mod
        $releases = $modModel->getReleases($id);
        
        $mod['releases'] = $releases;
        
        $this->response()->json([
            'status' => 'success',
            'data' => $mod
        ]);
    }
}