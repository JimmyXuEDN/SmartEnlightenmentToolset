为配合封装,对从官网下载的sdk作如下修改:
1.Config从const 改成public static(为了配置可以传值)
2.所有调用Config的地方都修改成调用static的形式.
3.Data增加AppPayParams,客户端的支付签名直接从服务器生成.
3.WxPay.Api.php  539,540行,curl方法增加
  curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//可以本地提交
  curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//可以本地提交
     以方便本地测试
     
4.本地测试可能需要固定ip(WxPay.Api.php 53行,换成固定ip)
5.增加autoload.php,方便加载