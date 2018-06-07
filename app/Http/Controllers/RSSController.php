<?php

namespace App\Http\Controllers;
use App\Posts;
use Bhaktaraz\RSSGenerator\Feed;
use Bhaktaraz\RSSGenerator\Channel;
use Bhaktaraz\RSSGenerator\Item;
use Michelf\Markdown;

class RSSController extends Controller
{
    public function retrieveRSS()
    {
        $feed = new Feed();
        $channel = new Channel();
        $channel
            ->title("Stanley's Blog")
            ->description("My heart will go on.")
            ->url(getenv('FRONTEND_BASE_URL'))
            ->appendTo($feed);
        
        $posts = Posts::skip(0)->orderBy('updated_at', 'desc')->take(10)->get();
        foreach($posts as $post) {
            $item = new Item();
            $item
            ->title($post->title ? $post->title : "Untitled")
            ->description($post->expand_content ? $post->revision->content : "No description available")
            ->url(getenv('FRONTEND_BASE_URL').'/post/'.$post->id)
            ->creator($post->revision->author->name)
            ->pubDate(strtotime($post->updated_at))
            ->category($post->category_id != 0 ? $post->category->name : 'Uncategorized')
            ->content(htmlspecialchars(Markdown::defaultTransform($post->revision->content)))
            ->appendTo($channel);
        }

        echo $feed;
    }
}
