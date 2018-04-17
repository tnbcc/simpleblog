<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
class SessionsController extends Controller
{
    public function __construct(){
        $this->middleware('guest',[
                'only' => ['create']
        ]);
    }

    /*
     *显示登录页面
     */
    public function create(){
       return view('sessions.create');
    }
    /*
     *登录信息验证&创建新会话
     */
    public function store(Request $request){
      $credentials = $this->validate($request,[
        'email' => 'required|email|max:255',
        'password' => 'required'
      ]);
      //使用Auth中的attempt方法对用户登录信息进行匹配
      if(Auth::attempt($credentials, $request->has('remember'))){
        //登录成功后的相关操作
        session()->flash('success','欢迎回来！');
        //使用Auth::user()方法获取当前用户登录信息,intended方法能将页面重定向到上一次尝试访问的页面上
        return redirect()->intended(route('users.show', [Auth::user()]));
      } else {
        //登录失败后的相关操作
        session()->flash('danger','很抱歉，您的邮箱和密码不匹配！');
        //使用withInput能将错误登录信息保留
        return redirect()->back()->withInput();
      }

    }
    /*
     *销毁会话登出
     */
     public function destory(){
          //使用Auth 中logout方法
          Auth::logout();
          session()->flash('success','您已成功退出！');
          return redirect('login');
     }
}
