<?php

use think\migration\Seeder;

class AuthMemberSeeder extends Seeder
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
                'member_id' => 1,
                'identity_type' => 6,
                'identifier' => '徐元庆',
                'credential' => 'FAF72C4383539D04',
                'open_id' => '100001',
                'auth_info' => '',
                'status' => 1,
                'create_time' => time(),
                'update_time' => time()
            ],
            [
                'member_id' => 1,
                'identity_type' => 1,
                'identifier' => '13826560282',
                'credential' => 'FAF72C4383539D04',
                'open_id' => '100001',
                'auth_info' => '',
                'status' => 1,
                'create_time' => time(),
                'update_time' => time()
            ]
        ];

        $posts = $this->table('auth_member');
        $posts->insert($data)
            ->save();
    }
}