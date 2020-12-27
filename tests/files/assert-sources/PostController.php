<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\Api\V1\PostRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * @group Post
 * APIs to manage Post
 */
class PostController extends Controller
{
    const RELATIONSHIPS = [

    ];

    /**
     * Display a listing of Post.
     *
     * @queryParam filter array An array to filter fields. Example: [name]="john"
     * @queryParam search string String to conduct full text search. Example: John Doe
     * @queryParam page_size int Number of items to return per page. Example: 50
     * @queryParam sort string Sort results by field. Example: -name will sort results by name in descending order
     * @apiResourceCollection App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, PostRepository $repository)
    {
        $pageSize = $request->page_size ?? 50;

        $posts = $repository->buildQuery()->with(self::RELATIONSHIPS);

        if ($search = $request->search) {
            $posts = $repository->search($search)->query(function (Builder $builder) use ($posts) {
                $builder->with(self::RELATIONSHIPS);
                $builder->whereIn('id', $posts->get()->pluck('id'));
            });
        }

        return PostResource::collection($posts->paginate($pageSize))->response();
    }

    /**
     * Store a newly created Post in storage.
     * @bodyParam title required string Post title. Example: Alice said very.
     * @bodyParam body required mediumText Post body. Example: Alice said very humbly; 'I won't have any rules in particular; at least, if.
     * @bodyParam user_id required foreignId Post user id. Example: 1
     * @bodyParam book_author_id required foreignId Post book author id. Example: 1
     * @bodyParam tags required pivot Post tags. Example: [1, 2]
     * @apiResource App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, PostRepository $repository)
    {
        $created = $repository->create($request->toArray());

        return (new PostResource($created))->response();
    }

    /**
     * Display the specified Post.
     *
     * @urlParam Post required Post ID
     * @apiResource App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param  \App\Models\$post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        return (new PostResource($post))->response();
    }

    /**
     * Update the specified Post in storage.
     * @bodyParam title required string Post title. Example: Alice said very.
     * @bodyParam body required mediumText Post body. Example: Alice said very humbly; 'I won't have any rules in particular; at least, if.
     * @bodyParam user_id required foreignId Post user id. Example: 1
     * @bodyParam book_author_id required foreignId Post book author id. Example: 1
     * @bodyParam tags required pivot Post tags. Example: [1, 2]
     * @urlParam Post required Post ID
     * @apiResource App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\$post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post, PostRepository $repository)
    {
        $updated = $repository->update($post, $request->toArray());

        return (new PostResource($updated))->response();
    }

    /**
     * Remove the specified Post from storage.
     *
     * @urlParam Post required Post ID
     * @apiResource App\Http\Resources\PostResource
     * @apiResourceModel App\Models\Post
     * @param  \App\Models\$post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post, PostRepository $repository)
    {
        $deleted = $repository->forceDelete($post);

        return (new PostResource($deleted->loadMissing(self::RELATIONSHIPS)))->response();
    }
}
