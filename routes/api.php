<?php

use Illuminate\Http\Request;
use App\Post;

Route::get('/ping', function () {
	return 'pong';
});

Route::post('/posts', function (Request $request) {
    $payload = $request->all();
    $post = Post::create($payload);

    return ['post' => ['id' => $post->id]];
});

Route::get('/posts', function (Request $request) {
    $tags = $request->input('tag');

    if ($tags) {
        $posts = Post::whereRaw("JSON_CONTAINS(tags, '" . json_encode($tags) . "')")->get();
    } else {
        $posts = Post::all();
    }

    return ['posts' => $posts];
});

Route::get('/posts/count', function (Request $request) {
    $tags = $request->input('tag');

    if ($tags) {
        $count = Post::whereRaw("JSON_CONTAINS(tags, '" . json_encode($tags) . "')")->count();
    } else {
        $count = Post::count();
    }

    return ['count' => $count];
});

Route::get('/posts/{post}', function (int $postId) {
    $post = Post::find($postId);

    if (!$post) {
        // abort(404);
         return response()->json([
            'message' => 'Record not found',
        ], 404);
    }

    return ['post' => $post];
});

Route::patch('/posts/{post}', function (Request $request, int $postId) {
    $payload = $request->all();
    $post = Post::find($postId);
    $post->update($payload);
});

Route::post('/posts/{post}/tags/{tag}', function (int $postId, string $tag) {
    $post = Post::find($postId);
    $post->tags = array_merge($post->tags, [$tag]);
    $post->save();
});

Route::delete('/posts/{post}/tags/{tag}', function (int $postId, string $tag) {
    $post = Post::find($postId);
    $post->tags = array_filter($post->tags, function($postTag) use ($tag) {
        return $postTag !== $tag;
    });
    $post->save();
});

Route::delete('/posts/{post}', function (int $postId) {
    // $flight->delete();
    Post::destroy($postId);
});
