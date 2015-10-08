<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
use Log;
use App\Http\Controllers\WeixinController;
use App\Http\Controllers\WxmediaController;

class DeleteNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weixin:deletenews {newsid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a news and remove the related mediaids from Weixin. ';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $newsid = $this->argument('newsid');
    	WxmediaController::deleteWxmedia($newsid);
    }
}
