<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class)->times(50)->make();
        User::insert($users->makeVisible(['password','remember_token'])->toArray());

        $user = User::find(1);
        $user->name = '孤独风中一匹马。';
        $user->email = 'tniub.cc@gmail.com';
        $user->password = bcrypt('zcaini1314');
        $user->img_path = '/photo/YwCI2pEoKfa5mOTSv4rzamnGBHvZchwCm4Z7Mfx6.jpeg';
        $user->is_admin = true;
        $user->save();
    }
}
