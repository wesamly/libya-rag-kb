<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    public $table = 'chat_history';
    
    // Only 'created_at' is managed by Eloquent
    public $timestamps = ['created_at'];
    const UPDATED_AT = null;

    protected $fillable = [
        'session_id',
        'user_question',
        'ai_response',
        'retrieved_article_ids',
        'user_feedback',
        'retrieved_context',
    ];

    protected $casts = [
        'retrieved_article_ids' => 'array',
    ];
}
