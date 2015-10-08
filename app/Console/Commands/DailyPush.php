<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\WeixinController;
use App\Http\Controllers\WxmediaController;

class DailyPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'weixin:dailypush {user}';
	protected $signature = 'weixin:dailypush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weixin Daily Push. SyncUser -> SubmitNews -> SendPreview';

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
        //$user = $this->argument('user');
        
    	$weixin = new \App\Http\Controllers\WeixinController();
    	
    	//$weixin->syncUser(true);
    	//$weixin->syncUser();
    	
    	//$newsid = $weixin->submitNews();
    	//echo "### $newsid \n";
    	
    	//$newsid = "ggmD3LwrOfOVaF8ty9nzg-xKV3u1JlN6Fyd-6Xj4qiY";
    	//$weixin->sendPushMsg($newsid, 100, false);
    	//$weixin->sendPushMsg();
    	
    	//WxmediaController::deleteWxmedia($newsid);
    }
}
