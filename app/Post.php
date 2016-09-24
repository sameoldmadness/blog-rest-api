<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PostQueryBuilder extends QueryBuilder
{
    function whereContainsTags(array $tags = null)
    {
        if ($tags === null) {
            return $this;
        }

        /** @TODO: Might be an injection, investigate */
        return $this->whereRaw("JSON_CONTAINS(tags, '" . json_encode($tags) . "')");
    }
}

class Post extends Model
{
    protected $fillable = ['title', 'body', 'tags'];
    protected $casts = ['tags' => 'array'];

    function addTag(string $tag) {
        $this->tags = array_unique(array_merge($this->tags, [$tag]));
    }

    function removeTag(string $tag) {
        $this->tags = array_filter($this->tags, function($postTag) use ($tag) {
            return $postTag !== $tag;
        });
    }

    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();
        $grammar = $conn->getQueryGrammar();

        return new PostQueryBuilder($conn, $grammar, $conn->getPostProcessor());
    }
}
