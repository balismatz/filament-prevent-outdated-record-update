<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Workbench\Database\Factories\PostFactory;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title'];

    /**
     * Get the posts that list this post as related.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, foreignPivotKey: 'related_post_id')
            ->withPivot('information')
            ->withTimestamps();
    }

    /**
     * Get the posts related to this post.
     */
    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, relatedPivotKey: 'related_post_id')
            ->withPivot('information')
            ->withTimestamps();
    }
}
