<?php

namespace App\Console\Commands;

use App\Services\StoryService;
use Illuminate\Console\Command;

class RemoveStory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:story';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove stories after 24hrs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $storyservice = new StoryService;
        return $storyservice->removestoryAfter24hrs();
    }
}
