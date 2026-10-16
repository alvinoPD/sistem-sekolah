<?php

namespace App\Http\Controllers;

use App\Models\posts;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user()->role === 'admin') {
            return redirect()->route('admin.beranda');
        }

        abort_unless($request->user()->role === 'user', 403);

        $posts = posts::latest()->paginate(10);

        return view('user.beranda-user', compact('posts'));
    }
}
