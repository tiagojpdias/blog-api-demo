<?php

namespace App\Http\Controllers;

use App\Filters\PostFilter;
use App\Http\Requests\Post\CreatePost;
use App\Http\Requests\Post\DeletePost;
use App\Http\Requests\Post\ListOwnPosts;
use App\Http\Requests\Post\ListPosts;
use App\Http\Requests\Post\ReadPost;
use App\Http\Requests\Post\UpdatePost;
use App\Http\Serializers\PostSerializer;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * List Posts.
     *
     * @param ListPosts      $request
     * @param PostFilter     $filter
     * @param PostRepository $postRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(ListPosts $request, PostFilter $filter, PostRepository $postRepository): JsonResponse
    {
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
     * List own Posts (including unpublished).
     *
     * @param ListOwnPosts   $request
     * @param PostFilter     $filter
     * @param PostRepository $postRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listOwn(ListOwnPosts $request, PostFilter $filter, PostRepository $postRepository): JsonResponse
    {
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
     * Read a Post.
     *
     * @param ReadPost $request
     * @param Post     $post
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(ReadPost $request, Post $post): JsonResponse
    {
        return response()->resource($post, new PostSerializer(), [
            'author',
        ]);
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

        return response()->resource($post, new PostSerializer(), [], 201);
    }

    /**
     * Update a Post.
     *
     * @param UpdatePost $request
     * @param Post       $post
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePost $request, Post $post): JsonResponse
    {
        if ($title = $request->get('title')) {
            $post->title = $title;
        }

        if ($content = $request->get('content')) {
            $post->content = $content;
        }

        if ($publishedAt = $request->get('published_at')) {
            $post->published_at = $publishedAt;
        }

        $post->save();

        return response()->resource($post, new PostSerializer());
    }

    /**
     * Delete a Post.
     *
     * @param DeletePost $request
     * @param Post       $post
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeletePost $request, Post $post): JsonResponse
    {
        try {
            $post->delete();

            return response()->resource($post, new PostSerializer());
        } catch (\Exception $exception) {
            return response()->jsonApiSpec([
                'errors' => [
                    'title'  => 'Internal Server Error',
                    'detail' => 'Unable to delete Post',
                ],
            ], 500);
        }
    }
}
