<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class UsersController extends Controller
{
    /*
     *未登录的用户只能访问所选页面  auth except
     *只允许未登录的人访问 guest only
     */
  public function __construct(){
       //允许游客访问的方法
        $this->middleware('auth',[
            'except' => ['show','create','store','index']
        ]);
        //登录后的用户无法再次访问注册页面
        $this->middleware('guest',[
            'only' => ['create']
        ]);
  }

  /*
   *列出所有用户页面
   */
  public function index(){
    $users = User::paginate(6);
     return view('users.index',compact('users'));
  }


  /*
   *用户注册页面
   */
    public function create(){
        return view('users.create');
    }

    /*
    *用户展示
    */
   public function show(User $user){
        return view('users.show',compact('user'));
   }

   /*
   *表单提交
   */
   public function store(Request $request){
       //验证表单
        $this->validate($request,[
          'name' => 'required|max:50',
          'email' => 'required|email|unique:users|max:255',
          'password' => 'required|confirmed|min:6'
        ]);
        //写进数据库
        $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password'=> bcrypt($request->password),
             'img_path' => '/photo/title.jpg',
        ]);
        //用户注册成功后自动登录
        Auth::login($user);
        session()->flash('success','欢迎，您将开启一段新的旅程~');
        return redirect()->route('users.show',[$user]);
   }

   /*
    *用户编辑页面显示
    */
    public function edit(User $user){
            //通过update 授权策略 用户只能进入到自己的编辑界面做更新操作
            if(\Auth::user()->can('update',$user))
          {
              return view('users.edit',compact('user'));
          }else{
              session()->flash('info','您无权编辑别人个人信息哟~');
              return redirect()->intended(route('users.edit', [Auth::user()]));
          }

            return view('users.edit',compact('user'));
    }

    /*
     *更新用户个人信息
     */
     public function update(User $user,Request $request){
           $this->validate($request,[
                 'name' => 'required|max:50',
                 'password' => 'nullable|confirmed|min:6'
           ]);
         //登录后的用户只能对自己信息做更新操作
         $this->authorize('update',$user);

           if($request->hasFile('photo') && $request->file('photo')->isValid()){
                  $photo = $request->file('photo');
                  $extension = $photo->extension();
                  $store_result = $photo->store('photo');
           }
           //exit('未获得上传');
           $ex = ['jpg','jpeg','png','gif'];
         $data = [];
       if($request->photo && in_array($extension,$ex)){
                $data['img_path'] = '/'.$store_result;
       }else{
              $data['img_path'] = $user->img_path;
       }
        //如果用户不想修改密码提交为空则将原密码作为更新密码

        $data['name'] = $request->name;
        if(!$request->password){
              $data['password'] = $user['password'];
        }else{
              $data['password'] = bcrypt($request->password);
        }
        $result = $user->update($data);
        if($result){
          session()->flash('success','个人资料更新成功~');
          return redirect()->route('users.show',$user->id);
        }else{
          session()->flash('danger','个人资料更新失败~');
          return redirect()->route('users.show',$user->id);
        }

     }

     /*
      *DELETE http 删除用户
      */
     public function destroy(User $user){
        //使用authorize方法对删除操作做授权验证
         $this->authorize('destroy',$user);
         $user->delete();
         session()->flash('success','成功删除用户！');
         return back();
     }
}
