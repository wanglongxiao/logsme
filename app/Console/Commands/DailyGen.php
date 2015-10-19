<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
use Log;
use App\Http\Controllers\WeixinController;
use App\Http\Controllers\WxmediaController;

class DailyGen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'weixin:dailygen {user}';
	protected $signature = 'weixin:dailygen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weixin Daily Job. SyncUser -> SubmitNews -> SendPreview to admin openid';

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
    	$weixin->syncUser();
    	
    	$newsid = $weixin->submitNews();
    	Log::error('Create news: '.$newsid);
    	
    	$weixin->wxHouseKeeping();
    	//$cmd = "rm -rf /tmp/*";
    	$cmd = "find /tmp -type f -mtime +7 -exec rm -rf {} \;";
    	system($cmd);
    }
}
