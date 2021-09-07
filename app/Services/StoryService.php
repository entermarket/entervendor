<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Carbon\Carbon;

class StoryService extends Controller
{
  public function addstory($request, $user)
  {
    return  $user->story()->create([
      'image' => $request->image,

    ]);
  }

  public function userstories($user)
  {
    return $user->story()->get();
  }

  public function removestoryAfter24hrs()
  {

    $stories = Story::get();
    $overdue = $stories->filter(function ($a) {
      $now = Carbon::now();
      $creation = Carbon::parse($a->created_at);
      $diff = $now->diffInHours($creation);

      if ($diff > 24) {
        return $a;
      }
    })->map(function ($b) {
      return $b->id;
    });
    Story::destroy($overdue);
    return 'story removed';
  }
}
