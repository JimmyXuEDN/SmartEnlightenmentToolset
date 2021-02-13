<?php
/**
 * Created by PhpStorm.
 * User: Geekgit
 * Date: 2018/1/31
 * Time: 17:44
 */

namespace wx\official\lib\api;
use wx\official\lib\Util;

/**
 * 素材管理
 *
 */
class Material extends Base
{
    /**
     * @param $type       form-data中媒体文件标识，有filename、filelength、content-type等信息
     * @param $file_path  临时素材的本地路径
     * @return mixed
     */
    public function addMaterialTemp($type,$file_path)
    {
        $url="media/upload?access_token=".Util::getAccessToken();
        $this->values['media']=new \CURLFile($file_path);
        $this->values['type'] = $type;
        return $this->post_form($url);

    }

    /**
     * @param $media_id   媒体文件ID
     * @return mixed
     */
    public function getMaterialTemp($media_id)
    {
        $url="media/get?access_token=".Util::getAccessToken();
        $this->values['media_id'] = $media_id;
        return $this->get($url);

    }


    public function addImage($file_path)
    {
        $url="media/uploadimg?access_token=".Util::getAccessToken();
        $this->values['media']=new \CURLFile($file_path);
        return $this->post_form($url);


    }

    /**
     * @param $title              标题
     * @param $thumb_media_id     图文消息的封面图片素材id（必须是永久mediaID）
     * @param null $author        作者
     * @param null $digest        图文消息的摘要
     * @param $show_cover_pic     是否显示封面，0为false，即不显示，1为true，即显示
     * @param $content            图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS,图片url必须来源 "上传图文消息内的图片获取URL"接口获取。
     * @param $content_source_url 图文消息的原文地址，即点击“阅读原文”后的URL
     * @return mixed
     */
    public function addMaterialStatic($article)
    {
        $url="material/add_news?access_token=".Util::getAccessToken();
        $this->values = $article;

        return $this->post($url);
    }

    /**
     * @param $media_id 要获取的素材的media_id
     * @return mixed
     */
    public function getMaterialStatic($media_id)
    {
        $url="material/get_material?access_token=".Util::getAccessToken();
        $this->values['media_id'] = $media_id;

        return $this->post($url);
    }


    /**
     * @param $media_id 要修改的图文消息的id
     * @param $index    要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
     * @param $title    标题
     * @param $thumb_media_id   图文消息的封面图片素材id（必须是永久mediaID）
     * @param $author   作者
     * @param $digest   图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     * @param $show_cover_pic 是否显示封面，0为false，即不显示，1为true，即显示
     * @param $content     图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
     * @param $content_source_url   图文消息的原文地址，即点击“阅读原文”后的URL
     * @return mixed
     */
    public function updateMaterial($media_id,$index,$title,$thumb_media_id,$author,$digest,$show_cover_pic,$content,$content_source_url)
    {
        $url = "material/update_news?access_token=".Util::getAccessToken();
        $this->values['media_id'] = $media_id;
        $this->values['index'] = $index;
        $this->values['title'] = $title;
        $this->values['thumb_media_id'] = $thumb_media_id;
        $this->values['author'] = $author;
        $this->values['digest'] = $digest;
        $this->values['show_cover_pic'] = $show_cover_pic;
        $this->values['content'] = $content;
        $this->values['content_source_url'] = $content_source_url;

        return $this->post($url);


    }
    
    /**
     * 新增其他类型永久素材
     * @param unknown $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param unknown $file_path 媒体文件的本地路径
     * @param unknown $description 上传视频素材时需要素材的描述信息，内容格式为JSON，格式如下：{"title":VIDEO_TITLE,"introduction":INTRODUCTION}
     */
    public function addMaterial($type,$file_path,$description=null){
       $url = "material/add_material?access_token=".Util::getAccessToken()."&type=".$type;
       
       $this->values['media']=new \CURLFile($file_path);
       if(!is_null($description)){
           $this->values['description']=$description;
       }
       return $this->post_form($url);
    }
    
    /**
     * 获取素材列表
     * @param unknown $type 素材类型:图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param unknown $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     * @param unknown $count 返回素材的数量，取值在1到20之间
     * @return mixed
     */
    public function getMaterialList($type,$offset,$count)
    {
        $url="material/batchget_material?access_token=".Util::getAccessToken();
        $this->values['type'] = $type;
        $this->values['offset'] = $offset;
        $this->values['count'] = $count;

        return $this->post($url);
    }

    /**
     * @param $media_id 要删除的媒体ID
     * @return mixed
     */
    public function deleteMaterial($media_id)
    {
        //删除永久素材
        $url="material/del_material?access_token=".Util::getAccessToken();
        $this->values['media_id'] = $media_id;

        return $this->post($url);

    }

}