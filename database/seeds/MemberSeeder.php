<?php

use think\migration\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'member_sn' => 100001,
                'qr_code' => '',
                'mobile' => '13826560282',
                'nick_name' => '清风徐徐',
                'avatar' => '',
                'gender' => 1,
                'birthday' => 0,
                'is_verify' => 1,
                'status' => 1,
                'create_time' => time(),
                'update_time' => time()
            ]
        ];

        $posts = $this->table('member');
        $posts->insert($data)
            ->save();
    }
}