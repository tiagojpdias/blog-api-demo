<?php

namespace App\Http\Controllers;

use App\Filters\PostFilter;
use App\Http\Requests\Post\ListPublishedPosts;
use App\Http\Serializers\PostSerializer;
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
}
