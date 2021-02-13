<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/18
 * Time: 16:05
 */

namespace app\base\model;

/**
 * Class Kuai100
 * @package general
 */

class Kuai extends BaseModel
{
    /**
     * 快递100破解接口
     * @param $exp_sn
     * @param $exp_code
     * @return null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getExpressQueryCrack($exp_sn, $exp_code)
    {
        $url="https://www.kuaidi100.com/query?type=".$exp_code."&postid=".$exp_sn;
        $header['Host'] = 'www.kuaidi100.com';
        $header['Referer'] = 'https://www.kuaidi100.com/';
        $header['Cookie'] = 'hWWWID=WWWFA94F6ABFD450E4B127EAF7C2C353D3F; Hm_lvt_22ea01af58ba2be0fec7c11b25e88e6c=1547778878; Hm_lpvt_22ea01af58ba2be0fec7c11b25e88e6c=1547778878';
        $header['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36';
        $result_data=json_decode($this->curl($url,'GET',$header,null),true);
        $info = null;
        if($result_data['status']!=200){
            // $this->sendResponse(ERROR_PARAMS, array(), $result_data["message"]);
            trace('快递100请求信息返回错误，返回内容：' . json_encode($result_data), 'error');
        }else{
            $exp_map['code'] = $exp_code;
            $exp = db('Exp')->where($exp_map)->find();
            $info['exp_name'] = $exp['name'];
            $info['exp_sn'] = $exp_sn;
            $info['exp_code'] = $exp_code;
            $info['detail'] = $result_data['data'];
        }
        return $info;
    }

    public function getExpressQuery($exp_sn, $exp_code)
    {
        //参数设置
        $post_data = array();
        $post_data["customer"] = 'F31A89C9AA4C4A1C91C9844E17C471E6';
        $key= 'lvfrAfEo6044' ;
        $post_data["param"] = '{"com":"' . $exp_code . '","num":"' . $exp_sn . '"}';

        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }
        $post_data=substr($o,0,-1);
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_URL,$url);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//        $result = curl_exec($ch);
        $curl = new \Curl\Curl();
        $curl->post($url, $post_data);
        $result_data = json_decode($curl->response,true);
        return $result_data;
    }

    public function curl($url,$method,$header=null,$data=null){
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//可以本地提交
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//可以本地提交
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if($data){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        }

        if($header){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $output=curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $output;
        }
    }

}