<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('profile.index');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'role' => ['required', 'in:admin,user'],
        ]);

        if (session()->has('user_id')) {
            $user = User::findOrFail(session('user_id'));
            $user->update($data);

            // Update session name
            session(['user_name' => $user->name]);
        }

        return redirect()->route('profile.index')->with('status', 'Profil berhasil diperbarui.');
    }
}
