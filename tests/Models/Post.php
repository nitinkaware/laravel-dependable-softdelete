<?php

namespace NitinKaware\DependableSoftDeletable\Test\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NitinKaware\DependableSoftDeletable\Traits\DependableDeleteTrait;

class Post extends Model {

    use DependableDeleteTrait, SoftDeletes;

    protected static $dependables = ['comments'];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
