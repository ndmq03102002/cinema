<?php

namespace App\Repositories\user\Comment;

use App\Models\Evaluate;
use App\Repositories\user\Movie\MovieRepository;
use Illuminate\Support\Facades\DB;

class CommentRepository
{
    public function createComment($data, $accountId)
    {
        $comment = Evaluate::where('account_id', $accountId)->where('movie_id', $data['movie_id'])->first();
        if($comment){
            $status = 'update';
            $data = Evaluate::find($comment['id'])->update([
                'movie_id' => $data['movie_id'],
                'vote_star' => $data['vote_star'],
                'comment' => $data['comment']
            ]);
            return [
                'data' => $comment,
                'status' => $status
            ];
        }
        else{
            $status = 'create';
            $data = Evaluate::create([
                'account_id' => $accountId,
                'movie_id' => $data['movie_id'],
                'vote_star' => $data['vote_star'],
                'comment' => $data['comment']
            ]);
            return [
                'data' => $data,
                'status' => $status
            ];
        }

    }
    public function getComment($movieId, $accountId = null)
    {
        $comment['by_user'] = DB::table('ci_evaluate as e')
            ->select('e.comment', 'e.vote_star', 'e.created_at', 'p.name', 'p.avatar')
            ->join('ci_profile as p', 'p.account_id', '=', 'e.account_id')
            ->where('e.movie_id', $movieId)
            ->where('e.account_id', $accountId)
            ->get();
        $comment['by_all'] = DB::table('ci_evaluate as e')
            ->select('e.comment', 'e.vote_star', 'e.created_at', 'p.name', 'p.avatar')
            ->join('ci_profile as p', 'p.account_id', '=', 'e.account_id')
            ->where('e.movie_id', $movieId)
//            ->where('e.account_id', '<>', $accountId)
            ->orderBy('created_at', 'DESC')
            ->get();
        return $comment;
    }
    public function deleteComment($movieId, $accountId = null)
    {
        $comment = Evaluate::select('ci_evaluate.*')->where('movie_id', $movieId)->where('account_id', $accountId)->first();
        $delete = Evaluate::where('movie_id', $movieId)->where('account_id', $accountId)->delete();
        return $comment;
    }
}
