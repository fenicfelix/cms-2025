<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DailyPageviews;
use App\Models\Post;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where("email", "=", $request->post("username"))->orWhere("username", "=", $request->post("username"))->first();

        if (!$user) return redirect("login")->withSuccess('Invalid login details.');

        $credentials = [
            "email" => $user->email,
            "password" => $request->post("password")
        ];
        if (Auth::attempt($credentials)) {
            $thumbnail = asset('backend/img/user/user5.svg');
            if ($user->thumbnail) $thumbnail = Storage::disk('public')->url($user->thumbnail);
            else $thumbnail = "https://ui-avatars.com/api/?name=" . $user->first_name . "+" . $user->last_name . "&color=00339c&background=a7beed";
            session(["name" => $user->first_name]);
            session(['thumbnail' => $thumbnail]);

            if (in_array($user->group_id, [1, 2])) return redirect('/')->with('success', 'Welcome back ' . $user->first_name);
            else if (in_array($user->group_id, [3, 4])) return redirect(route('posts.index', 'all'))->with('success', 'Welcome back ' . $user->first_name);
            else return redirect(route('/'))->with('success', 'Welcome back ' . $user->first_name);
        }

        return redirect("/login")->with('error', 'Invalid login details.');
    }
}
