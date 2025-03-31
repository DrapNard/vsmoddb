<?php

namespace App\Controllers\Api\V1;

use App\Core\Controller;
use App\Models\User;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = User::has('mods')->with('mods')->get();
        
        return $this->json([
            'success' => true,
            'authors' => $authors->map(function($author) {
                return [
                    'id' => $author->id,
                    'username' => $author->username,
                    'avatar_url' => $author->avatar_url,
                    'mods_count' => $author->mods->count(),
                    'total_downloads' => $author->mods->sum('downloads_count'),
                    'joined_at' => $author->created_at
                ];
            })
        ]);
    }
}