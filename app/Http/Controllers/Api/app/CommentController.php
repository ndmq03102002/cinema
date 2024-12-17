<?php

namespace App\Http\Controllers\Api\app;

use App\Enums\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CinemaRequest;
use App\Models\Cinema;
use App\Models\Evaluate;
use App\Models\Movie;
use App\Repositories\user\Comment\CommentRepository;
use App\Repositories\user\Movie\MovieRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public $movieRepository;

    public $commentRepository;
    public function __construct(
        MovieRepository $movieRepository,
        CommentRepository $commentRepository
    )
    {
        $this->movieRepository = $movieRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @author son.nk
     * @OA\Post (
     *     path="/api/app/comment/create",
     *     tags={"App Đánh giá"},
     *     summary="Tạo mới hoặc cập nhật",
     *     operationId="app/comment/create",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="movie_id", type="bigint"),
     *              @OA\Property(property="comment", type="string"),
     *              @OA\Property(property="vote_star", type="integer"),
     *          @OA\Examples(
     *              summary="Examples",
     *              example = "Examples",
     *              value = {
     *                  "movie_id": 10,
     *                  "comment": "Phim rất hay",
     *                  "vote_star": 5,
     *                  },
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function createComment(Request $request){
        try {
            DB::beginTransaction();
            $data = $request->all();
            $comment = $this->commentRepository->createComment($data, $this->getCurrentLoggedIn()->id);
            if($comment['status'] == 'create'){
                $movieDetail = $this->movieRepository->getMovieDetail($data['movie_id']);
                if($movieDetail->voting){
                    $movieDetail->voting = 0;
                    $movieDetail->vote_total = 0;
                }
                $movie = Movie::where('id', $data['movie_id'])->first();
                if ($movie) {
                    $newVoteTotal = (int)$movie['vote_total'] + 1;
                    $newVoting = ((float)$movie->vote_total * (float)$movie->voting + $data['vote_star']) / $newVoteTotal;

                    $movie->update([
                        'voting' => $newVoting,
                        'vote_total' => $newVoteTotal,
                    ]);
                }
            }else{
                $movieDetail = $this->movieRepository->getMovieDetail($data['movie_id']);
                if($movieDetail->voting){
                    $movieDetail->voting = 0;
                    $movieDetail->vote_total = 0;
                }
                $movie = Movie::find($data['movie_id']);
                if ($movie) {
                    $dataOld = $comment['data'];
                    $newVoteTotal = $movie->vote_total;
                    $newVoting = ((float)$movie->vote_total * (float)$movie->voting + $data['vote_star'] - $dataOld['vote_star']) / $newVoteTotal;

                    $movie->update([
                        'voting' => $newVoting,
                        'vote_total' => $newVoteTotal,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $comment
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
     * @author son.nk
     * @OA\Get (
     *     path="/api/app/comment/get/{id}",
     *     tags={"App Đánh giá"},
     *     summary="Lấy theo id phim",
     *     operationId="app/comment/get",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID movie",
     *          required=true,
     *               @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function getComment($id){
        try {
            if($this->getCurrentLoggedIn())
                $data = $this->commentRepository->getComment($id, $this->getCurrentLoggedIn()->id);
            else{
                $data = $this->commentRepository->getComment($id);
            }
            if (!$data) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => trans('messages.errors.cinema.id_found'),
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $data
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
     * @author son.nk
     * @OA\Delete (
     *     path="/api/app/comment/delete/{id}",
     *     tags={"App Đánh giá"},
     *     summary="Xóa comment theo id phim",
     *     operationId="app/comment/delete",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID movie",
     *          required=true,
     *               @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *             @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success."),
     *          )
     *     ),
     * )
     */
    public function deleteComment($id){
        try {
            DB::beginTransaction();
            if($this->getCurrentLoggedIn()){
                $data = $this->commentRepository->deleteComment($id, $this->getCurrentLoggedIn()->id);
                $movie = Movie::where('id', $data->movie_id)->first();
                if ($movie) {
                    $newVoteTotal = (int)$movie['vote_total'] - 1;
                    if($newVoteTotal > 0)
                        $newVoting = ((float)$movie->vote_total * (float)$movie->voting + $data['vote_star']) / $newVoteTotal;
                    else
                        $newVoting = 0;
                    $movie->update([
                        'voting' => $newVoting,
                        'vote_total' => $newVoteTotal,
                    ]);
                }
            }
            if (!$data) {
                return response()->json([
                    'status' => Constant::FALSE_CODE,
                    'message' => trans('messages.errors.cinema.id_found'),
                    'data' => []
                ], Constant::SUCCESS_CODE);
            }
            DB::commit();
            return response()->json([
                'status' => Constant::SUCCESS_CODE,
                'message' => trans('messages.success.success'),
                'data' => $data
            ], Constant::SUCCESS_CODE);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => Constant::FALSE_CODE,
                'message' => $th->getMessage(),
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
