<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use App\Services\OSS;
use Mail;

class UsersController extends Controller
{
    /*
     *未登录的用户只能访问所选页面  auth except
     *只允许未登录的人访问 guest only
     */
  public function __construct(){
       //允许游客访问的方法
        $this->middleware('auth',[
            'except' => ['show','create','store','index','confirmEmail']
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

       $statuses = $user->statuses()
                        ->orderBy('created_at','desc')
                        ->paginate('15');
        return view('users.show',compact('user','statuses'));
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
        //用户注册成功后提示用户进行账号激活
        //Auth::login($user);
        $this->sendEmailConfirmationTo($user);
        //dd($this->sendEmailConfirmationTo($user));
        session()->flash('success','验证邮件已发送到您的注册邮箱上，请注意查收。');
        return redirect('/');
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
                  $file = $request->file('photo');
                  $pic = $file->getRealPath();
                  $extension = $file->extension();
                  $key = \Auth::id() . '_' . time() . '_' . str_random(10) . '.' .$extension;
                  $result = OSS::upload($key,$pic);
                  $path = 'https://ccblogs.oss-cn-beijing.aliyuncs.com/'.$key;
           }
           //exit('未获得上传');
           $ex = ['jpg','jpeg','png','gif'];
         $data = [];
       if($request->photo && in_array($extension,$ex) && $result){
                $data['img_path'] = $path;
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
      *发送邮件激活
      */
      protected function sendEmailConfirmationTo($user)
        {
            $view = 'emails.confirm';
            $data = compact('user');
            $from = 'hero_sky_c@163.com';
            $name = 'CcBlog';
            $to = $user->email;
            $subject = "感谢注册 CcBlog 应用！请确认你的邮箱。";

            Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
                $message->from($from, $name)->to($to)->subject($subject);
            });
        }

     /*
      *确认激活
      */

      public function confirmEmail($token)
      {
         $user = User::where('activation_token',$token)->firstOrFail();

         $user->activated = true;
         $user->activation_token = null;
         $user->save();

         Auth::login($user);
         session()->flash('success','恭喜您，激活成功！');
         return redirect()->route('users.show',[$user]);
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

     /*
      *显示用户关注人列表
      */
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(15);
        $title = '关注的人';
        return view('users.show_follow',compact('users','title'));
    }

     /*
      *显示粉丝列表
      */
    public function followers(User $user)
    {
       $users = $user->followers()->paginate(15);
       $title = '粉丝';
      return view('users.show_follow',compact('users','title'));
    }

























}
