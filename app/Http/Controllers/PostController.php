<?php

namespace App\Http\Controllers;

use App\Filters\PostFilter;
use App\Http\Requests\Post\CreatePost;
use App\Http\Requests\Post\ListPrivatePosts;
use App\Http\Requests\Post\ListPublishedPosts;
use App\Http\Serializers\PostSerializer;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * List published Posts.
     *
     * @param ListPublishedPosts $request
     * @param PostFilter         $filter
     * @param PostRepository     $postRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPublished(
        ListPublishedPosts $request,
        PostFilter $filter,
        PostRepository $postRepository
    ): JsonResponse {
        $filter->sortBy($request->input('sort', 'created_at'), $request->input('order', 'desc'))
            ->setItemsPerPage($request->input('per_page', 10))
            ->setPageNumber($request->input('page', 1));

        // Make sure we only get published Posts
        $filter->withPublished(true);

        if ($authors = $request->input('authors')) {
            $filter->withAuthors($authors);
        }

        $posts = $postRepository->getPaginator($filter);

        return response()->paginator($posts, new PostSerializer(), [
            'author',
        ]);
    }

    /**
     * List private (own) Posts.
     *
     * @param ListPrivatePosts $request
     * @param PostFilter       $filter
     * @param PostRepository   $postRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPrivate(
        ListPrivatePosts $request,
        PostFilter $filter,
        PostRepository $postRepository
    ): JsonResponse {
        $filter->sortBy($request->input('sort', 'created_at'), $request->input('order', 'desc'))
            ->setItemsPerPage($request->input('items', 10))
            ->setPageNumber($request->input('page', 1));

        // Make sure we only get Posts from the current User
        $filter->withAuthors([
            $request->user('api')->id,
        ]);

        $published = $request->input('published');

        if ($published !== null) {
            $filter->withPublished($published);
        }

        $posts = $postRepository->getPaginator($filter);

        return response()->paginator($posts, new PostSerializer());
    }

    /**
     * Create a Post.
     *
     * @param CreatePost $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreatePost $request): JsonResponse
    {
        $post = new Post();

        $post->author_id = $request->user()->id;
        $post->title = $request->get('title');
        $post->content = $request->get('content');

        if ($publishedAt = $request->get('published_at')) {
            $post->published_at = $publishedAt;
        }

        $post->save();

        return response()->resource($post, new PostSerializer());
    }
}
