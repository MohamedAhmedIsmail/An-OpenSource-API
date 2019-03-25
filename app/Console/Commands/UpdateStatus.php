<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\MyMail;
use App\Url;
use App\User;
use Mail;
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
            /* if my site is up now after down
            *  calculate the duration of down
            *  get the difference between the down status and the new up status
            *  notify the user that his website is up after down with the time duration
            */
            if($this->checkUrlStatus($url->url) && $url->status == 0)
            {
                $oldDate = $url->created_at;
                $oldTime = strtotime($oldDate);
                $currentDate = time();
                $timeDifference = $currentDate-$oldTime;
                $years = floor($timeDifference / (365*60*60*24));
                $months = floor(($timeDifference - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($timeDifference - $years * 365*60*60*24 -  $months*30*60*60*24)/ (60*60*24));
                $newDate = $years . " years " . $months . " months " . $days . " days";
                $user = User::where('id','=',$url->user_id)->first();
                $this->mailDuration($user->email,$url->url,$newDate);
            }
            /* check for the urls status using curl function
            *  if they are running and up
            *  update their status in database
            */
            if($this->checkUrlStatus($url->url))
            {
                $url->status = 1;
                $url->save();
                $this->Info($url->url." is up and running");
            }
            /* check for the urls status using curl function
            *  if they are down
            *  update their status in database
            *  send mail to user that the website is down
            */
            else
            {
                $url->status = 0;
                $url->save();
                $this->Info($url->url." is down");
                $user = User::where('id','=',$url->user_id)->first();
                $this->mail($user->email,$url->url);
            }
        }
    }
    /*
    * function to check the status of the website up or down
    * using curl function
    */
    public function checkUrlStatus($url)
    {
        $agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        $ch = curl_init();
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
        if($httpcode >= 200 && $httpcode < 400)
        { 
            return true;
        }
        else
        { 
            return false;
        }
    }
    /*
    * mail function take the userEmail and his website
    * send mail to the user if his website is down
    */
    public function mail($userEmail,$url)
    {
        Mail::send('email.myemail', ['url' => $url], function ($m) use ($userEmail) 
        {
            $m->from('no-reply@test.com', 'no-reply');
            $m->to($userEmail)->subject('Website is down');
        });
    }
    /*
    * mailDuration function take the userEmail, his website and the duration 
    * send mail to the user if his website is up after down with the duration of down
    */
    public function mailDuration($userEmail,$url,$duration)
    {
        Mail::send('email.emailduration', ['url' => $url,'duration'=>$duration], function ($m) use ($userEmail) 
        {
            $m->from('no-reply@test.com', 'no-reply');
            $m->to($userEmail)->subject('Website is down');
        });
    }
}
