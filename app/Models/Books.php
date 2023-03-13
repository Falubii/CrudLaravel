<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'publication_date'
    ];
    public function authors()
    {
        return $this->belongsToMany(Authors::class, 'books_authors');
    }

}
