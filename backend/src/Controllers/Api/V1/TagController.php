<?php

namespace App\Controllers\Api\V1;

use App\Core\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        
        return $this->json([
            'success' => true,
            'tags' => $tags->map(function($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'description' => $tag->description,
                    'type' => $tag->type,
                    'count' => $tag->mods_count
                ];
            })
        ]);
    }
}