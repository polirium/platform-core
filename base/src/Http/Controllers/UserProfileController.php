<?php

namespace Polirium\Core\Base\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function profile()
    {
        return view('core/base::user.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function settings()
    {
        return view('core/base::user.settings', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return redirect()->back()->with('success', __('Cập nhật hồ sơ thành công!'));
    }
}
