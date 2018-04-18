<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
  /*
   * users模型监听器 在用户创建前生成用户激活令牌
   */
  public function creating(User $user)
  {
       $user->activation_token = str_random(30);
  }
}
