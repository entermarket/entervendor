<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StoryService;

class StoryController extends Controller
{
    public $storyservice;
    public $user;

    public function __construct(StoryService $storyservice)
    {
        $this->storyservice = $storyservice;
        $this->user = auth('api')->user();
    }
    public function index()
    {
        return $this->storyservice->userstories($this->user);
    }

    public function store(Request $request)
    {
        return $this->storyservice->addstory($request, $this->user);
    }

    public function show(Story $story)
    {
        return $story;
    }

    public function destroy(Story $story)
    {
        $story->delete();
        return $this->response_success('story removed');
    }

    public function remove()
    {
        return $this->storyservice->removestoryAfter24hrs();
    }
}
