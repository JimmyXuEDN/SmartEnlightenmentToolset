<?php

namespace app\mp\controller;

use app\base\controller\ApiBaseController;
use app\mp\model\Mp;

class ApiMp extends ApiBaseController
{
    public function test_mp_qr($code)
    {
        $mp = Mp::where(['code' => $code])->find();
        $data['scene'] = 'test';
        $data['page'] = '';
        $data['width'] = 800;
        // echo $mp->qr($data);
        // $this->sendResponse(0, ['data' => $mp->qr($data)]);
        header('Content-type: image/jpg');
        // echo $mp->qr($data);
        echo '<img src="data:png;base64,' . base64_encode($mp->qr($data)) . '" />';
    }
}