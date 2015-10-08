<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
use Log;
use App\Http\Controllers\WeixinController;
use App\Http\Controllers\WxmediaController;

class HourlyPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'weixin:hourlypush {user}';
	protected $signature = 'weixin:hourlypush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Weixin Hourly Job. Send Msg To Users (check whether match the schudule)';

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
    	
    	//$newsid = "";
    	$weixin->sendPushMsg($newsid, Config::get("weixin.wxtestergroup"), false);
    	//$weixin->sendPushMsg();
    }
}
