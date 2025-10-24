<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'status',
        'is_spam'
    ];

    protected $casts = [
        'is_spam' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Mutators per sanitizzazione automatica
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strip_tags(trim($value));
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setSubjectAttribute($value)
    {
        $this->attributes['subject'] = strip_tags(trim($value));
    }

    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = strip_tags(trim($value));
    }

    // Scope per query comuni
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopeNotSpam($query)
    {
        return $query->where('is_spam', false);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Metodo per marcare come letto
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    // Metodo per verificare se è spam
    public function checkSpam()
    {
        $spamWords = ['viagra', 'casino', 'lottery', 'winner', 'click here'];
        $messageContent = strtolower($this->message . ' ' . $this->subject);

        foreach ($spamWords as $word) {
            if (str_contains($messageContent, $word)) {
                $this->update(['is_spam' => true]);
                return true;
            }
        }

        return false;
    }
}
