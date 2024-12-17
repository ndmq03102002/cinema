<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Movie_Genre;
use App\Models\MovieShowtime;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Rated;
use App\Repositories\user\Movie\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Http\Requests\admin\MovieRequest;
use App\Enums\Constant;
use Cloudinary\Cloudinary;

/**
 * @OA\Schema(
 *     schema="Movie",
 *     type="object",
 *     required={"id", "name", "description", "duration", "date"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Phim Hành Động Mới"),
 *     @OA\Property(property="slug", type="string", example="phim-hanh-dong-moi"),
 *     @OA\Property(property="country_id", type="integer", example=1),
 *     @OA\Property(property="rated_id", type="integer", example=1),
 *     @OA\Property(property="avatar", type="string", example="https://example.com/avatar.jpg"),
 *     @OA\Property(property="poster", type="string", example="https://example.com/poster.jpg"),
 *     @OA\Property(property="trailer_url", type="string", example="https://youtube.com/trailer"),
 *     @OA\Property(property="duration", type="integer", example=120),
 *     @OA\Property(property="date", type="string", format="date", example="2024-10-10"),
 *     @OA\Property(property="performer", type="string", example="Diễn viên chính"),
 *     @OA\Property(property="director", type="string", example="Đạo diễn nổi tiếng"),
 *     @OA\Property(property="description", type="string", example="Mô tả phim"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-09T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-09T12:00:00Z")
 * )
 */

class MovieController extends Controller
{
    public $movieRepository;
    public function __construct(
        MovieRepository $movieRepository
    )
    {
        $this->movieRepository = $movieRepository;
    }
    public function movieIndex(){
        $movies = Movie::all();
        return view('admin.movie.index',compact('movies'));
    }
    public function movieCreate(){
        $config['method'] = 'create';
        $genre = Genre::pluck('name', 'id');
        $rated = Rated::pluck('name', 'id');
        $country = Country::pluck('name', 'id');
        return view('admin.movie.create',compact('config','genre','rated','country'));
    }
    public function movieStore(MovieRequest $request)
    {
        try {
            DB::beginTransaction();
            // Upload ảnh avatar lên Cloudinary
            $avatarUrl = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarResult = cloudinary()->upload($avatar->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $avatarUrl = $avatarResult->getSecurePath(); // Lấy URL an toàn
            }

            // Upload ảnh poster lên Cloudinary
            $posterUrl = null;
            if ($request->hasFile('poster')) {
                $poster = $request->file('poster');
                $posterResult = cloudinary()->upload($poster->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $posterUrl = $posterResult->getSecurePath();
            }

            $data = $request->all();
            $movie = new Movie();
            $movie->name = $data['name'];
            $movie->slug = $data['slug'];
            $movie->country_id = $data['country_id'];
            $movie->rated_id = $data['rated_id'];
            $movie->avatar = $avatarUrl;
            $movie->poster = $posterUrl;
            $movie->trailer_url = $data['trailer_url'];
            $movie->duration = $data['duration'];
            $movie->date = $data['date'];
            $movie->performer = $data['performer'];
            $movie->director = $data['director'];
            $movie->description = $data['description'];
            $movie->status = 1;
            foreach($data['genre_ids'] as $key => $gen){
                $movie->genre_id = $gen[0];
            }
            $movie->save();

            // Lưu tất cả genre_id vào bảng ci_movie_genre sử dụng attach
            $movie->movie_genre()->attach($request->input('genre_ids'));

            DB::commit();

            return redirect()
                ->route('movie.create')
                ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()
                ->route('movie.create')
                ->with('error', $th->getMessage());
        }
    }


    public function movieEdit(string $id){
        $movie = Movie::find($id);
        $genre = Genre::pluck('name', 'id');
        $rated = Rated::pluck('name', 'id');
        $country = Country::pluck('name', 'id');
        $config['method'] = 'edit';
        return view('admin.movie.create', compact('config','movie','genre','rated','country'));
    }

    public function movieUpdate($id, MovieRequest $request){
        try {
            DB::beginTransaction();

            $movie = Movie::findOrFail($id);

            // Lưu đường dẫn ảnh cũ
            $oldAvatar = $movie->avatar;
            $oldPoster = $movie->poster;
            if ($oldAvatar) {
                $path = parse_url($oldAvatar, PHP_URL_PATH);
                $parts = explode('/movie/', $path);
                $avatarPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            }
            if ($oldPoster) {
                $path = parse_url($oldPoster, PHP_URL_PATH);
                $parts = explode('/movie/', $path);
                $posterPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            }
            // Cập nhật thông tin phim
            $movie->update($request->only([
                'name',
                'slug',
                'country_id',
                'rated_id',
                'trailer_url',
                'duration',
                'date',
                'performer',
                'director',
                'description',
                'status'
            ]));

            // Xử lý upload avatar nếu có
            if ($request->hasFile('avatar')) {
                $avatarResult = cloudinary()->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $movie->avatar = $avatarResult->getSecurePath();

                // Xóa ảnh cũ trên Cloudinary
                if ($oldAvatar) {
                    cloudinary()->destroy($avatarPart); // Xóa ảnh cũ
                }
            } else {
                $movie->avatar = $oldAvatar;
            }

            // Xử lý upload poster nếu có
            if ($request->hasFile('poster')) {
                $posterResult = cloudinary()->upload($request->file('poster')->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $movie->poster = $posterResult->getSecurePath();

                // Xóa ảnh cũ trên Cloudinary
                if ($oldPoster) {
                    cloudinary()->destroy($posterPart); // Xóa ảnh cũ
                }
            } else {
                $movie->poster = $oldPoster;
            }

            // Lưu thông tin phim
            $movie->save();

            // Sử dụng sync để cập nhật mối quan hệ giữa phim và thể loại
            $movie->movie_genre()->sync($request->input('genre_ids'));

            DB::commit();

            return redirect()
            ->route('movie.index')
            ->with('success', trans('messages.success.success'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()
            ->route('movie.index')
            ->with('error', $th->getMessage());
        }

    }
    public function updateAjax(Request $request, $id)
    {
        if ($request->ajax()) {
            $movie = Movie::find($id);

            // Cập nhật trạng thái
            if ($request->has('status')) {
                $movie->status = $request->status;
            }
            $movie->save();

            return response()->json(['success' => 'Thông tin đã được cập nhật']);
        }
    }
    public function movieDestroy($id)
{
    try {
        DB::beginTransaction();

        // Tìm phim
        $movie = Movie::findOrFail($id);

        // Xóa ảnh avatar trên Cloudinary
        $oldAvatar = $movie->avatar;
        if ($oldAvatar) {
            $path = parse_url($oldAvatar, PHP_URL_PATH);
            $parts = explode('/movie/', $path);
            $avatarPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME);
            cloudinary()->destroy($avatarPart);
        }

        // Xóa ảnh poster trên Cloudinary
        $oldPoster = $movie->poster;
        if ($oldPoster) {
            $path = parse_url($oldPoster, PHP_URL_PATH);
            $parts = explode('/movie/', $path);
            $posterPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME);
            cloudinary()->destroy($posterPart);
        }

        // Xóa các liên kết `movie_genre`
        Movie_Genre::where('movie_id', $movie->id)->delete();
        MovieShowtime::where('movie_id', $movie->id)->delete();
        // Xóa phim
        $movie->delete();

        DB::commit();

        // Trả về thông báo thành công
        return redirect()->back()->with('success', 'Xóa movie thành công.');
    } catch (\Throwable $th) {
        DB::rollBack();

        // Trả về thông báo lỗi nếu có
        return redirect()->back()->with('error', 'Xóa movie thất bại: ' . $th->getMessage());
    }
}

    /**
     * @author quynhndmq
     * @OA\Get(
     *     path="/api/admin/movies",
     *     tags={"Admin Movies"},
     *     summary="Get all movies",
     *     operationId="getMovies",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Movie")
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
            $movies = Movie::with('movie_genre')->get();
            if ($movies->isEmpty()) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Movies not found',
                    'data' => []
                ], 200);
            }
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $movies
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
     *     path="/api/admin/movies",
     *     tags={"Admin Movies"},
     *     summary="Create a new movie",
     *     operationId="createMovie",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="country_id", type="integer"),
     *             @OA\Property(property="rated_id", type="integer"),
     *             @OA\Property(property="avatar", type="string"),
     *             @OA\Property(property="poster", type="string"),
     *             @OA\Property(property="trailer_url", type="string"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="performer", type="string"),
     *             @OA\Property(property="director", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="genre_ids", type="array", @OA\Items(type="integer")),
     *             @OA\Property(property="status", type="integer"),
     *             @OA\Examples(
     *                 example="CreateMovieExample",
     *                 summary="Sample movie creation data",
     *                 value={
     *                     "name": "Phim Hành Động Mới",
     *                     "slug": "phim-hanh-dong-moi",
     *                     "country_id": 1,
     *                     "rated_id": 1,
     *                     "avatar": "https://example.com/avatar.jpg",
     *                     "poster": "https://example.com/poster.jpg",
     *                     "trailer_url": "https://youtube.com/trailer",
     *                     "duration": 120,
     *                     "date": "2024-10-10",
     *                     "performer": "Diễn viên chính",
     *                     "director": "Đạo diễn nổi tiếng",
     *                     "description": "Mô tả phim",
     *                     "status": 1,
     *                     "genre_ids": {1, 2},
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Movie created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Movie created successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Movie")
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
    public function store(MovieRequest $request)
    {
        try {
            DB::beginTransaction();

            // Kiểm tra genre_ids có tồn tại không
            foreach ($request->input('genre_ids') as $genreId) {
                if (!Genre::find($genreId)) {
                    return response()->json([
                        'status' => Constant::FALSE_CODE,
                        'message' => "Genre ID {$genreId} does not exist."
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            // Upload ảnh avatar lên Cloudinary
            $avatarUrl = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarResult = cloudinary()->upload($avatar->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $avatarUrl = $avatarResult->getSecurePath(); // Lấy URL an toàn
            }

            // Upload ảnh poster lên Cloudinary
            $posterUrl = null;
            if ($request->hasFile('poster')) {
                $poster = $request->file('poster');
                $posterResult = cloudinary()->upload($poster->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $posterUrl = $posterResult->getSecurePath();
            }

            // Tạo phim mới
            $movie = Movie::create([
                'name' => $request->input('name'),
                'slug' => $request->input('slug'),
                'genre_id' => $request->input('genre_ids')[0],
                'country_id' => $request->input('country_id'),
                'rated_id' => $request->input('rated_id'),
                'avatar' => $avatarUrl,
                'poster' => $posterUrl,
                'trailer_url' => $request->input('trailer_url'),
                'duration' => $request->input('duration'),
                'date' => $request->input('date'),
                'performer' => $request->input('performer'),
                'director' => $request->input('director'),
                'description' => $request->input('description'),
                'status' => 1
            ]);

            // Lưu tất cả genre_id vào bảng ci_movie_genre sử dụng attach
            $movie->movie_genre()->attach($request->input('genre_ids'));

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Movie created successfully',
                'data' => $movie
            ], 200);
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
     *     path="/api/admin/movies/{id}",
     *     tags={"Admin Movies"},
     *     summary="Get a movie by ID",
     *     operationId="getMovieById",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of movie",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Movie")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Movie not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $movie = Movie::with('movie_genre')->find($id);

        if (!$movie) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Movie not found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Movie retrieved successfully',
            'data' => $movie
        ],200);
    }

    /**
     * @author quynhndmq
     * @OA\Put(
     *     path="/api/admin/movies/{id}",
     *     tags={"Admin Movies"},
     *     summary="Update a movie",
     *     operationId="updateMovie",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of movie to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="country_id", type="integer"),
     *             @OA\Property(property="rated_id", type="integer"),
     *             @OA\Property(property="avatar", type="string"),
     *             @OA\Property(property="poster", type="string"),
     *             @OA\Property(property="trailer_url", type="string"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="performer", type="string"),
     *             @OA\Property(property="director", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="integer"),
     *             @OA\Property(property="genre_ids", type="array", @OA\Items(type="integer")),
     *             @OA\Examples(
     *                 example="UpdateMovieExample",
     *                 summary="Sample movie update data",
     *                 value={
     *                     "name": "Phim Hành Động Mới Cập Nhật",
     *                     "slug": "phim-hanh-dong-moi-cap-nhat",
     *                     "country_id": 1,
     *                     "rated_id": 1,
     *                     "avatar": "https://example.com/avatar_updated.jpg",
     *                     "poster": "https://example.com/poster_updated.jpg",
     *                     "trailer_url": "https://youtube.com/trailer_updated",
     *                     "duration": 130,
     *                     "date": "2024-10-12",
     *                     "performer": "Diễn viên chính cập nhật",
     *                     "director": "Đạo diễn nổi tiếng cập nhật",
     *                     "description": "Mô tả phim cập nhật",
     *                     "status": 1,
     *                     "genre_ids": {1, 2}
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Movie updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Movie updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/Movie")
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
    public function update(MovieRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $movie = Movie::findOrFail($id);
            if (!$movie) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Movie not found',
                    'data' => []
                ], 200);
            }
            // Lưu đường dẫn ảnh cũ
            $oldAvatar = $movie->avatar;
            $oldPoster = $movie->poster;
            if ($oldAvatar) {
                $path = parse_url($oldAvatar, PHP_URL_PATH);
                $parts = explode('/movie/', $path);
                $avatarPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            }
            if ($oldPoster) {
                $path = parse_url($oldPoster, PHP_URL_PATH);
                $parts = explode('/movie/', $path);
                $posterPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            }
            // Cập nhật thông tin phim
            $movie->update($request->only([
                'name',
                'slug',
                'country_id',
                'rated_id',
                'trailer_url',
                'duration',
                'date',
                'performer',
                'director',
                'description',
                'status'
            ]));

            // Xử lý upload avatar nếu có
            if ($request->hasFile('avatar')) {
                $avatarResult = cloudinary()->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $movie->avatar = $avatarResult->getSecurePath();

                // Xóa ảnh cũ trên Cloudinary
                if ($oldAvatar) {
                    cloudinary()->destroy($avatarPart); // Xóa ảnh cũ
                }
            } else {
                $movie->avatar = $oldAvatar;
            }

            // Xử lý upload poster nếu có
            if ($request->hasFile('poster')) {
                $posterResult = cloudinary()->upload($request->file('poster')->getRealPath(), [
                    'folder' => 'movie',
                    'upload_preset' => 'movie-upload',
                ]);
                $movie->poster = $posterResult->getSecurePath();

                // Xóa ảnh cũ trên Cloudinary
                if ($oldPoster) {
                    cloudinary()->destroy($posterPart); // Xóa ảnh cũ
                }
            } else {
                $movie->poster = $oldPoster;
            }

            // Lưu thông tin phim
            $movie->save();

            // Sử dụng sync để cập nhật mối quan hệ giữa phim và thể loại
            $movie->movie_genre()->sync($request->input('genre_ids'));

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Movie updated successfully',
                'data' => $movie
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
     *     path="/api/admin/movies/{id}",
     *     tags={"Admin Movies"},
     *     summary="Delete a movie",
     *     operationId="deleteMovie",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of movie to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Movie deleted successfully",
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

            $movie = Movie::findOrFail($id);
            if (!$movie) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => 'Movie not found',
                    'data' => []
                ], 200);
            }
            // $oldAvatar = $movie->avatar;
            // $oldPoster = $movie->poster;
            // if ($oldAvatar) {
            //     $path = parse_url($oldAvatar, PHP_URL_PATH);
            //     $parts = explode('/movie/', $path);
            //     $avatarPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            //     cloudinary()->destroy($avatarPart);
            // }
            // if ($oldPoster) {
            //     $path = parse_url($oldPoster, PHP_URL_PATH);
            //     $parts = explode('/movie/', $path);
            //     $posterPart = 'movie/' . pathinfo($parts[1], PATHINFO_FILENAME); // 'avatar/khx9uvzvexda7dniu5sa'
            //     cloudinary()->destroy($posterPart);
            // }

            // Xóa các genre liên quan
            Movie_Genre::where('movie_id', $movie->id)->delete();
            MovieShowtime::where('movie_id', $movie->id)->delete();
            $movie->delete();

            DB::commit();

            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => 'Movie deleted successfully',
                'data' => []
            ], Response::HTTP_OK); // Sử dụng mã trạng thái 200 OK
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
     *     path="/api/app/movies/get-list",
     *     tags={"App Movies"},
     *     summary="Get movies by genre and status",
     *     operationId="getMoviesByGenreAndStatus",
     *     @OA\Parameter(
     *         name="genre_id",
     *         in="query",
     *         description="ID of genre to filter movies",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="statusShow",
     *         in="query",
     *         description="Filter movies by status (1: Current Movies, 2: Upcoming Movies, otherwise all movies)",
     *         required=false,
     *         @OA\Schema(type="integer", enum={1, 2})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Movie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movies not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Movies not found.")
     *         )
     *     )
     * )
     */
    public function getMovies(Request $request)
    {
        $genre_id = $request->query('genre_id'); // Genre ID để lọc phim
        $statusShow = $request->query('statusShow'); // Trạng thái phim

        $today = now()->toDateString(); // Ngày hiện tại

        // Tạo query cơ bản, eager load các mối quan hệ với genre, country và rated
        $query = Movie::with(['movie_genre', 'country', 'rated'])->where('status', 1);

        // Lọc theo genre_id nếu có
        if (!empty($genre_id)) {
            $query->whereHas('movie_genre', function ($subQuery) use ($genre_id) {
                $subQuery->where('genre_id', $genre_id);
            });
        }

        // Lọc theo statusShow
        if ($statusShow == 1) { // Phim đang chiếu
            $query->where('date', '<=', $today);
        } elseif ($statusShow == 2) { // Phim sắp chiếu
            $query->where('date', '>', $today);
        }

        $movies = $query->get();

        // Nếu không tìm thấy phim nào
        if ($movies->isEmpty()) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'movies not found',
                'data' => []
            ], 200);
        }

        // Transform data to include names instead of IDs
        $transformedMovies = $movies->map(function ($movie) {
            return [
                "id" => $movie->id,
                "name" => $movie->name,
                "slug" => $movie->slug,
                "genre" => $movie->movie_genre->map(function ($genre) {
                    return [
                        "id" => $genre->id,
                        "name" => $genre->name,
                    // other fields
                    ];
                }),
                "country" => $movie->country ? $movie->country->name : null,
                "rated" => $movie->rated ? $movie->rated->name : null,
                "avatar" => $movie->avatar,
                "poster" => $movie->poster,
                "trailer_url" => $movie->trailer_url,
                "duration" => $movie->duration,
                "date" => $movie->date,
                "performer" => $movie->performer,
                "director" => $movie->director,
                "description" => $movie->description,
                "vote_total" => $movie->vote_total,
                "voting" => $movie->voting,
                "created_at" => $movie->created_at,
                "updated_at" => $movie->updated_at,
                "status" => $movie->status,
                "deleted_at" => $movie->deleted_at,
            ];
        });

        // Trả về kết quả
        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Movies retrieved successfully',
            'data' => $transformedMovies
        ],200);
    }

    // user
    /**
     * @author son.nk
     * @OA\Get(
     *     path="/api/app/movies/show/{id}",
     *     tags={"App Movies"},
     *     summary="Lấy phim theo id",
     *     operationId="/api/app/movies",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of movie",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Movie")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Movie not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Movie not found.")
     *         )
     *     )
     * )
     */
    public function getMovieDetail($id)
    {
        if($this->getCurrentLoggedIn())
            $movie = $this->movieRepository->getMovieDetail($id, $this->getCurrentLoggedIn()->id);
        else
            $movie = $this->movieRepository->getMovieDetail($id);
        if (!$movie) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => 'Movie not found.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => Constant::SUCCESS_CODE,
            'message' => 'Movie retrieved successfully',
            'data' => $movie
        ],200);
    }

}
