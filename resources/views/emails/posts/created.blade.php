<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>{{ $post->title }}</h1>
        <p>{{ $post->body }}</p>
        <p>Tags: {{ $post->tags }}</p>
    </body>
</html>
