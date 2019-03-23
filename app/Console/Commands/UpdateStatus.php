<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Url;
class UpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:UpdateStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all registered urls and save their status in database';

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
        $urls=Url::all();
        foreach($urls as $url)
        {
            if($this->checkUrlStatus($url->url))
            {
                $url->status=1;
                $url->save();
                $this->Info($url->url);
            }
           
        }
    }
    public function checkUrlStatus($url)
    {
        $agent="Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch,CURLOPT_VERBOSE,false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_SSLVERSION,3);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
        $page=curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode>=200 && $httpcode<400)
        { 
            return true;
        }
        else
        { 
            return false;
        }
    }
}
