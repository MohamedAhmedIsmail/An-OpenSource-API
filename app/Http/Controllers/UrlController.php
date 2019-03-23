<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Url;
use JWTAuth;
class UrlController extends Controller
{
    protected $user;
    public function __construct()
    {
      $this->user=JWTAuth::parseToken()->authenticate();
    }
    public function index()
    {
      return $this->user->Url()->get(['url','status'])->toArray();
    }
    public function show($id)
    {
      $url=$this->user->Url()->find($id);
      if(!$url)
      {
        return response()->json([
          'success'=>false,
          'message'=>'Sorry the url with id '.$id . 'Cant be found' 
        ],400);
      }
      return $url;
    }
    public function store(Request $request)
    {
      $this->validate($request,[
        'url'=>'required',
        'status'=>'required'
      ]);
      $url=new Url;
      $url->url=$request->url;
      $url->status=$request->status;
      if($this->user->Url()->save($url))  
      {
        return response()->json([
          'success'=>true,
          'url'=>$url
        ]);
      }
      else
      {
        return response()->json([
          'success'=>false,
          'message'=>'Sorry, url could not be added'
        ],500);
      }
    }
    public function update(Request $request,$id)
    {
        $url=$this->user->Url()->find($id);
        if(!$url)
        {
          return response()->json([
            'success'=>false,
            'message'=>'Sorry url with id '. $id . 'cannot be found'
          ],400);
        }
        $url->update($request->all());
        if($url)
        {
          return response()->json([
            'success' => true
          ]);
        }
        else 
        {
          return response()->json([
              'success' => false,
              'message' => 'Sorry, url could not be updated'
          ], 500);
      }
    }
  public function destroy($id)
  {
    $url = $this->user->Url()->find($id);
    if (!$url) 
    {
        return response()->json([
            'success' => false,
            'message' => 'Sorry, url with id ' . $id . ' cannot be found'
        ], 400);
    }
    if ($url->delete()) 
    {
        return response()->json([
            'success' => true
        ]);
    } 
    else 
    {
        return response()->json([
            'success' => false,
            'message' => 'Url could not be deleted'
        ], 500);
    }
  }
}