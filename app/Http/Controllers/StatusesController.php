<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StatusesRequest;
use App\Models\Status;
use Auth;

class StatusesController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
   }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatusesRequest $request,Status $status)
    {
        //dd(Auth::id());
        $status->create([
             'user_id' => Auth::id(),
             'content' => $request->content
        ]);
        return redirect()->back();
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Status $status)
    {
        $this->authorize('destroy',$status);
        $status->delete();
        session()->flash('success','微博被成功删除！');
        return redirect()->back();
    }
}
