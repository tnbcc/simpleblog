<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','img_path'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
      $this->notify(new ResetPassword($token));
    }

    /**
      *一个用户拥有多条微博
      */

      public function statuses()
      {
         return $this->hasMany(Status::class);
      }

      public function feed()
      {
      $user_ids = Auth::user()->followings->pluck('id')->toArray();
       array_push($user_ids, Auth::user()->id);
       return Status::whereIn('user_id', $user_ids)
                             ->with('user')
                             ->orderBy('created_at', 'desc');
      }

      /**
       *一个用户(粉丝)能关注多个人、被关注者能拥有多个粉丝这是一种多对多的关系
       *一个人拥有多个粉丝
       */

      public function followers()
      {
         return $this->belongsToMany(User::class,'followers','user_id','follower_id');
      }

      /*
       *获取关注列表
       */
      public function followings()
      {
         return $this->belongsToMany(User::class,'followers','follower_id','user_id');
      }

     /*
      *执行关注操作
      */

      public function follow($user_ids)
      {
          if(!is_array($user_ids)){
               $user_ids = compact('user_ids');
          }
          $this->followings()->sync($user_ids,false);
      }

      /*
       *执行取消关注操作
       */
       public function unfollow($user_ids)
       {
          if(!is_array($user_ids)){
              $user_ids = compact('user_ids');
          }
          $this->followings()->detach($user_ids);
       }

       /*
        *判断当前用户A是否关注了用户B
        */

        public function isFollowing($user_id)
        {
           return $this->followings->contains($user_id);
        }















}
