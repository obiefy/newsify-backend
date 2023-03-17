<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function __invoke(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required', 'in:follow,unfollow',
                'item' => 'required', 'in:'. User::ITEM_CATEGORY .','. User::ITEM_SOURCE,
                'value' => 'required',
            ]);
            
            $request->user()->{$validated['type']}($validated['item'], $validated['value']);

            return $this->ok([
                'categories' => $request->user()->categories,
                'sources' => $request->user()->sources,
            ]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}
