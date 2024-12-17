<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Enums\Constant;
use App\Http\Requests\admin\GenreRequest;
use App\Models\Movie_Genre;
use App\Models\Movie;
use App\Models\MovieShowTime;

/**
 * @OA\Schema(
 *     schema="Genre",
 *     type="object",
 *     required={"id", "name", "description"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Hành động"),
 *     @OA\Property(property="description", type="string", example="Phim có những cảnh quay mạnh mẽ"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
 * )
 */
class GenreController extends Controller
{

    public function genreIndex(){
        $genres = Genre::all();
        return view('admin.genre.index',compact('genres'));
    }
    public function genreCreate(){
        $config['method'] = 'create';
        return view('admin.genre.create',compact('config'));
    }
    public function genreStore(GenreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            Genre::create($data);
            DB::commit();
            return redirect()
                ->route('genre.create')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->route('genre.create')
                ->with('error', $th->getMessage());
        }
        
    }

    public function genreEdit(string $id){
        $genre = Genre::find($id);
        $config['method'] = 'edit';
        return view('admin.genre.create', compact('config','genre'));
    }
  
    public function genreUpdate($id, GenreRequest $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            $query = Genre::find($id);
            $query->update($data);
            DB::commit();
            return redirect()
            ->route('genre.index')
            ->with('success', trans('messages.success.success'));

        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
            ->route('genre.index')
            ->with('error', $th->getMessage());
        }
    }
    public function genreDestroy(string $id){
        
        $movies = Movie::where('genre_id', $id)->get();

            foreach ($movies as $movie) {
               // Xóa các bản ghi trong bảng ci_movie_show_time liên kết với movie_id
                MovieShowTime::where('movie_id', $movie->id)->delete();
                // Xóa các liên kết trong bảng movie_genre
                Movie_Genre::where('movie_id', $movie->id)->delete();
                
                // Xóa phim
                $movie->delete();
            }
        Genre::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa genre thành công.');
    }


    /**
     * @author quynhndmq
     * @OA\Get(
     *     path="/api/admin/genres",
     *     tags={"Admin Genres"},
     *     summary="Get all genres",
     *     operationId="getGenres",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Genre")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $genres = Genre::all();
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $genres
            ], Constant::SUCCESS_CODE);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @author quynhndmq
     * @OA\Post(
     *     path="/api/admin/genres",
     *     tags={"Admin Genres"},
     *     summary="Create a new genre",
     *     operationId="createGenre",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Examples(
     *                 example="CreateGenreExample",
     *                 summary="Sample genre creation data",
     *                 value={
     *                     "name": "Hành động",
     *                     "description": "Phim có những cảnh quay mạnh mẽ"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Genre created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Genre created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Genre")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function store(GenreRequest $request)
    {
        try {
            DB::beginTransaction();

            $genre = Genre::create($request->all());

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Genre created successfully',
                'data' => $genre
            ], Response::HTTP_CREATED);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @author quynhndmq
     * @OA\Get(
     *     path="/api/admin/genres/{id}",
     *     tags={"Admin Genres"},
     *     summary="Get a genre by ID",
     *     operationId="getGenreById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of genre",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Genre")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Genre not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Genre not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $genre = Genre::find($id);
        if(!$genre){
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Genre not found',
                'data' => []
            ], Response::HTTP_OK);
        }
        // Route Model Binding nên không cần find('id') mà truyền thẳng object vào hàm
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Genre retrieved successfully',
            'data' => $genre
        ]);
    }

    /**
     * @author quynhndmq
     * @OA\Put(
     *     path="/api/admin/genres/{id}",
     *     tags={"Admin Genres"},
     *     summary="Update a genre",
     *     operationId="updateGenre",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of genre to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Examples(
     *                 example="UpdateGenreExample",
     *                 summary="Sample genre update data",
     *                 value={
     *                     "name": "Hài",
     *                     "description": "Phim hài hước với những tình huống dở khóc dở cười"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Genre updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Genre updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Genre")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The name has already been taken.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function update(GenreRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $genre = Genre::findOrFail($id);
            if(!$genre){
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Genre not found',
                    'data' => []
                ], 200);
            }
            $genre->update($request->all());

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Genre updated successfully',
                'data' => $genre
            ],200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @author quynhndmq
     * @OA\Delete(
     *     path="/api/admin/genres/{id}",
     *     tags={"Admin Genres"},
     *     summary="Delete a genre",
     *     operationId="deleteGenre",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of genre to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Genre deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $movies = Movie::where('genre_id', $id)->get();

            foreach ($movies as $movie) {
               // Xóa các bản ghi trong bảng ci_movie_show_time liên kết với movie_id
                MovieShowTime::where('movie_id', $movie->id)->delete();
                // Xóa các liên kết trong bảng movie_genre
                Movie_Genre::where('movie_id', $movie->id)->delete();
                
                // Xóa phim
                $movie->delete();
            }
            $genre = Genre::findOrFail($id);
            if(!$genre){
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Genre not found',
                    'data' => []
                ], 200);
            }
            $genre->delete();

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Genre deleted successfully',
                'data' => []
            ], Response::HTTP_OK); // Sử dụng 200 OK hoặc 202 Accepted
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
