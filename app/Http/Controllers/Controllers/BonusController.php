<?php

namespace app\Http\Controllers;

use App\Models\Bonus;
use App\Models\BonusHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BonusController extends Controller
{
    public function index(Request $request): View
    {
        $bonus = Bonus::where('user_id', $request->user()->id)->first();
        $bonus_histories = BonusHistory::where('user_id', $request->user()->id)->get();


        return view('user.bonus', compact('bonus', 'bonus_histories'));
    }
}
