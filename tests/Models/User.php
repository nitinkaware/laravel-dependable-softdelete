<?php

namespace NitinKaware\DependableSoftDeletable\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NitinKaware\DependableSoftDeletable\Traits\DependableDeleteTrait;

class User extends Model {

    use DependableDeleteTrait, SoftDeletes;

    protected static $dependables = ['posts'];

    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
