<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\WordQuestion;

class WordQuestionController extends Controller
{
    /**
     * 単語問題の登録
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\WordQuestion $wordQuestion
     * @return void
     */
    public function create(Request $request, WordQuestion $wordQuestion ) {
        // Log::info($request);
        $request->validate([
            'group_id' => 'required',
            'japanese' => 'required|string|min:1|max:20',
            'choice1' => 'required|string|min:1|max:30',
            'choice2' => 'required|string|min:1|max:30',
            'choice3' => 'required|string|min:1|max:30',
            'answer' => 'required|integer|min:1|max:3',
        ]);
        $wordQuestion->group_id = $request['group_id'];
        $wordQuestion->japanese = $request['japanese'];
        $wordQuestion->choice1 = $request['choice1'];
        $wordQuestion->choice2 = $request['choice2'];
        $wordQuestion->choice3 = $request['choice3'];
        $wordQuestion->answer = $request['answer'];

        $wordQuestion->save();
    }

    /**
     * idに合った問題を取得する
     * @param int $data
     * @return Array
     */
    public function read($data)
    {
        $id = $data; // $dataは、word_questionsのid
        if (ctype_digit($id)) {
            $wordQuestion = WordQuestion::where('id', $id)->first();
            // 検索結果がない場合には、エラーコード404を返却する
            if ($wordQuestion === null) { return abort(404); }
            return $wordQuestion;
        } else {
            // URLのID部分に数値でない入力でリクエストがあった場合にも、エラーコード404を返却する
            return abort(404);
        }
    }

    /**
     * グループに合った問題を取得する
     * @param string $group_id
     * @return Array
     */
    public function readgroup($group_id)
    {
        $wordQuestion = WordQuestion::where('group_id', $group_id)->get();
        // 検索結果がない場合には、エラーコード404を返却する
        if ($wordQuestion === null) { return abort(404); }
        return $wordQuestion;
    }

    /**
     * 問題を全て取得する
     * @param null
     * @return Array
     */
    public function readall()
    {
        // $wordQuestions = WordQuestion::all();
        $wordQuestions = WordQuestion::orderBy(WordQuestion::CREATED_AT, 'desc')->paginate(3);
        // 検索結果がない場合には、エラーコード404を返却する
        if ($wordQuestions === null) { return abort(404); }
        return $wordQuestions;
    }

    /**
     * 問題の更新
     * @param \Illuminate\Http\Request $request
     * @param int $data
     * @return Void
     */
    public function update (Request $request, $id)
    {
        // Log::info($request);
        // 1.この操作をする権限があるかどうかを確認する
        if (ctype_digit($id)) {
            $wordQuestion = WordQuestion::find($id);
            // 検索結果がない場合には、エラーコード404を返却する
            if ($wordQuestion === null) { return abort(404); }
        } else {
            // URLのID部分に数値でない入力でリクエストがあった場合にも、エラーコード404を返却する
            return abort(404);
        }
        // 1.バリデーションチェック
        $request->validate([
            'selected_group_id' => 'required',
            'japanese' => 'required|string|min:1|max:20',
            'choice1' => 'required|string|min:1|max:30',
            'choice2' => 'required|string|min:1|max:30',
            'choice3' => 'required|string|min:1|max:30',
            'answer' => 'required|integer|min:1|max:3',
        ]);

        // 2.変更がある項目のみセットする
        if ( $wordQuestion->group_id !== $request['selected_group_id'] ) { $wordQuestion->group_id = $request['selected_group_id']; }
        if ( $wordQuestion->japanese !== $request['japanese'] ) { $wordQuestion->japanese = $request['japanese']; }
        if ( $wordQuestion->choice1 !== $request['choice1']) { $wordQuestion->choice1 = $request['choice1']; }
        if ( $wordQuestion->choice2 !== $request['choice2']) { $wordQuestion->choice2 = $request['choice2']; }
        if ( $wordQuestion->choice3 !== $request['choice3']) { $wordQuestion->choice3 = $request['choice3']; }
        if ( $wordQuestion->answer !== $request['answer'] ) { $wordQuestion->answer = $request['answer']; }

        // 3.DBに保存
        $wordQuestion->save();
    }
}
