<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\History;

class HistoryController extends Controller
{
    /**
     * 履歴の登録
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\History $history
     * @return void
     */
    public function create(Request $request, History $history ) {
        // Log::info($request);
        $request->validate([
            'user_id' => 'required',
            'group_id' => 'required',
            'number_of_correct_answers' => 'required|integer',
            'number_answers' => 'required|integer',
        ]);
        $history->user_id = $request['user_id'];
        $history->group_id = $request['group_id'];
        $history->number_of_correct_answers = $request['number_of_correct_answers'];
        $history->number_answers = $request['number_answers'];

        $history->save();
    }

    /**
     * 履歴を全て取得する
     * @param null
     * @return Array
     */
    public function readall()
    {
        $histories = History::orderBy(History::CREATED_AT, 'desc')->paginate(3);
        // 検索結果がない場合には、エラーコード404を返却する
        if ($histories === null) { return abort(404); }
        return $histories;
    }

    /**
     * user_idに合った問題を取得する
     * @param string $user_id
     * @return Array
     */
    public function readuser($user_id)
    {
        $histories = History::where('user_id', $user_id)
            ->orderBy(History::CREATED_AT, 'desc')
            ->paginate(3);
        // 検索結果がない場合には、エラーコード404を返却する
        if ($histories === null) { return abort(404); }
        return $histories;
    }
}
