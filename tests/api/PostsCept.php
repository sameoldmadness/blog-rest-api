<?php

use \Codeception\Util\HttpCode;

$I = new ApiTester($scenario);

$I->wantTo('create a post');
$I->sendPOST('/posts', [
    'title' => 'Post title',
    'body' => 'Post body',
    'tags' => ['trending', 'cats'],
]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseMatchesJsonType([
    'post' => ['id' => 'integer'],
]);
$postId = $I->grabDataFromResponseByJsonPath('post.id')[0];

$I->wantTo('fetch all posts');
$I->sendGET("/posts");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'posts' => [[
        'id' => $postId,
        'title' => 'Post title',
        'body' => 'Post body',
        'tags' => ['trending', 'cats'],
    ]],
]);

$I->wantTo('fetch the post by id');
$I->sendGET("/posts/$postId");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'post' => [
        'id' => $postId,
        'title' => 'Post title',
        'body' => 'Post body',
        'tags' => ['trending', 'cats'],
    ],
]);

$I->wantTo('get all posts count');
$I->sendGET("/posts/count");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'count' => 1,
]);

// ['trending', 'cats']

$I->wantTo('fetch all posts by tag');
$I->sendGET("/posts?tag[]=trending");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'posts' => [[
        'id' => $postId,
        'title' => 'Post title',
        'body' => 'Post body',
        'tags' => ['trending', 'cats'],
    ]],
]);

$I->wantTo('count all posts by tag');
$I->sendGET("/posts/count?tag[]=trending");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'count' => 1,
]);

$I->wantTo('fetch all posts by multiple tags');
$I->sendGET("/posts?tag[]=trending&tag[]=cats");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'posts' => [[
        'id' => $postId,
        'title' => 'Post title',
        'body' => 'Post body',
        'tags' => ['trending', 'cats'],
    ]],
]);

$I->wantTo('count all posts by multiple tags');
$I->sendGET("/posts/count?tag[]=trending&tag[]=cats");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'count' => 1,
]);

$I->wantTo('fetch all posts by too many tags');
$I->sendGET("/posts?tag[]=trending&tag[]=cats&tag[]=immposibru");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'posts' => [],
]);

$I->wantTo('count all posts by too many tags');
$I->sendGET("/posts/count?tag[]=trending&tag[]=cats&tag=immposibru");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'count' => 0,
]);

$I->wantTo('overwrite the post');
$I->sendPUT("/posts/$postId", [
    'title' => 'Another post title',
]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->sendGET("/posts/$postId");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'title' => 'Another post title',
    'body' => '',
    'tags' => [],
]);

$I->wantTo('modify the post');
$I->sendPatch("/posts/$postId", [
    'body' => 'Another post body',
]);
$I->seeResponseCodeIs(HttpCode::OK);
$I->sendGET("/posts/$postId");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'title' => 'Another post title',
    'body' => 'Another post body',
    'tags' => [],
]);

$I->wantTo('add tag to the post');
$I->sendPOST("/posts/$postId/tags/cats");
$I->seeResponseCodeIs(HttpCode::OK);
$I->sendGET("/posts/$postId");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'tags' => ['cats'],
]);

$I->wantTo('remove tag from the post');
$I->sendDELETE("/posts/$postId/tags/cats");
$I->seeResponseCodeIs(HttpCode::OK);
$I->sendGET("/posts/$postId");
$I->seeResponseCodeIs(HttpCode::OK);
$I->seeResponseContainsJson([
    'tags' => [],
]);

$I->wantTo('delete the post');
$I->sendDelete("/posts/$postId");
$I->seeResponseCodeIs(HttpCode::OK);
$I->sendGET("/posts/$postId");
$I->seeResponseCodeIs(HttpCode::NOT_FOUND);
