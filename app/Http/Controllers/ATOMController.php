<?php

namespace App\Http\Controllers;
use App\Posts;
use Michelf\Markdown;
use \FeedWriter\ATOM;

class ATOMController extends Controller
{
    public function retrieveATOM()
    {
        $feed = new ATOM();
        $feed->setTitle("Stanley's Blog");
        $feed->setDescription("My heart will go on.");
        $feed->setLink(getenv('FRONTEND_BASE_URL'));

        $posts = Posts::skip(0)->orderBy('updated_at', 'desc')->take(5)->get();
        foreach($posts as $post) {
            $item = $feed->createNewItem();
            $item->setTitle($post->title ? $post->title : "Untitled");
            $item->setDescription($post->expand_content ? $post->revision->content : "No description available");
            $item->setLink(getenv('FRONTEND_BASE_URL').'/post/'.$post->id);
            $item->setAuthor($post->revision->author->name);
            $item->setDate(strtotime($post->updated_at));
            $item->setContent(Markdown::defaultTransform($post->revision->content));
            $feed->addItem($item);
        }

        $feed->printFeed();
    }
}
