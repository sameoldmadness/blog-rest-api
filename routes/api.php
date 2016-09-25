<?php

use Illuminate\Http\Request;
use App\Post;
use App\Mail\PostCreated;

Route::get('/ping', function () {
	return 'pong';
});

Route::post('/posts', function (Request $request) {
    $payload = $request->all();
    $post = Post::create($request->all());

    $email = config('mail.admin');
    Mail::to($email)->queue(new PostCreated($post));

    return ['post' => ['id' => $post->id]];
});

Route::get('/posts', function (Request $request) {
    $tags = $request->input('tag');
    $posts = Post::whereContainsTags($tags)->get();

    return ['posts' => $posts];
});

Route::get('/posts/count', function (Request $request) {
    $tags = $request->input('tag');
    $count = Post::whereContainsTags($tags)->count();

    return ['count' => $count];
});

Route::get('/posts/{post}', function (Post $post) {
    return ['post' => $post];
});

Route::patch('/posts/{post}', function (Request $request, Post $post) {
    $payload = $request->all();
    $post->update($payload);
});

Route::post('/posts/{post}/tags/{tag}', function (Post $post, string $tag) {
    $post->addTag($tag);
    $post->save();
});

Route::delete('/posts/{post}/tags/{tag}', function (post $post, string $tag) {
    $post->removeTag($tag);
    $post->save();
});

Route::delete('/posts/{post}', function (int $postId) {
    Post::destroy($postId);

    Log::info("Post deleted: $postId");
});
