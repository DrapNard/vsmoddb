<?php

namespace App\Controllers\Api\V1;

use App\Core\Controller;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['user', 'mod'])
            ->latest()
            ->paginate(20);
        
        return $this->json([
            'success' => true,
            'comments' => $this->formatComments($comments->items()),
            'pagination' => [
                'total' => $comments->total(),
                'per_page' => $comments->perPage(),
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage()
            ]
        ]);
    }

    public function forAsset($assetId)
    {
        $comments = Comment::with(['user', 'mod'])
            ->where('mod_id', $assetId)
            ->latest()
            ->paginate(20);
        
        return $this->json([
            'success' => true,
            'comments' => $this->formatComments($comments->items()),
            'pagination' => [
                'total' => $comments->total(),
                'per_page' => $comments->perPage(),
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage()
            ]
        ]);
    }

    private function formatComments($comments)
    {
        return $comments->map(function($comment) {
            return [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'user' => [
                    'id' => $comment->user->id,
                    'username' => $comment->user->username,
                    'avatar_url' => $comment->user->avatar_url
                ],
                'mod' => [
                    'id' => $comment->mod->id,
                    'name' => $comment->mod->name
                ]
            ];
        });
    }
}