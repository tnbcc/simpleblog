<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
/*
 *用户只能对自己的信息做编辑
 */
 public function update(User $currentUser, User $user){
       return $currentUser->id === $user->id;
 }
 /*
  *只有管理员才能做删除用户操作并且管理员不能删除自己
  */
public function destroy(User $currentUser, User $user){
    return $currentUser->is_admin && $currentUser->id !== $user->id;
}

}
