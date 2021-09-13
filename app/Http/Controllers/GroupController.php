<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Group;

class GroupController extends Controller
{
    /**
     * グループの登録
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Group $group
     * @return void
     */
    public function create(Request $request, Group $group ) {
        $request->validate([
            'name' => 'required|string|min:1|max:20',
        ]);
        $group->name = $request['name'];
        $group->save();
    }

    /**
     * グループを全て取得する
     * @param null
     * @return Array
     */
    public function readall()
    {
        $group = Group::orderBy(Group::CREATED_AT, 'desc')->get();
        return $group;
    }
}
