<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

/** Serves profile photos from the private disk (works without the public/storage symlink). Signed-in users only. */
class AvatarController extends Controller
{
    public function show(User $user)
    {
        $disk = Storage::disk(User::AVATAR_DISK);
        abort_unless($user->avatar_path && $disk->exists($user->avatar_path), 404);

        return $disk->response($user->avatar_path, null, [
            'Cache-Control' => 'private, max-age=604800',   // the URL changes with each new photo (?v=)
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}