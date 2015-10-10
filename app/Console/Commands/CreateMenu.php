<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Config;
use Log;
use App\Http\Controllers\WeixinController;
use App\Http\Controllers\WxmediaController;

class CreateMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weixin:createmenu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete existed menu and create a new one';

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
        $weixin = new \App\Http\Controllers\WeixinController();
    	$weixin->createMenu();
    }
}
