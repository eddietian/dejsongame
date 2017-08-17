<?php

namespace Media\Controller;
use Think\Controller;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class DownController extends Controller {
    
    public function down_file($game_id=0,$type=1){
        $model = M('Game','tab_');
        $map['tab_game.id'] = $game_id;
        // $map['file_type'] = $type;
        $data = $model
        ->field('tab_game_source.*,tab_game.game_name,tab_game.and_dow_address,tab_game.id as g_id,tab_game.ios_dow_address,tab_game.add_game_address,tab_game.ios_game_address')
        ->join("left join tab_game_source on tab_game.id = tab_game_source.game_id")->where($map)->order('file_type asc')->select();
        $first_data=reset($data);
        $end_data=end($data);
        // var_dump($end_data);exit;
        if($type==1){
            switch ($first_data['file_type']) {
                case ''://没上传原包
                    if(varify_url($first_data['add_game_address'])){
                        Header("HTTP/1.1 303 See Other");
                        Header("Location: ".$first_data['add_game_address']);
                    }else{
                        $this->error('下载地址错误！');
                    }
                break;
                case 1://如果设置了安卓包
                    if($first_data['and_dow_address']!=''&&$first_data['add_game_address']==""){
                        $this->down($first_data['and_dow_address'],$type);
                    }else if(varify_url($first_data['add_game_address'])){
                        Header("HTTP/1.1 303 See Other");
                        Header("Location: ".$first_data['add_game_address']);
                    }else{
                        $this->error('下载地址错误！');
                    }
                break;
                default:
                    if(varify_url($first_data['add_game_address'])){
                        Header("HTTP/1.1 303 See Other");
                        Header("Location: ".$first_data['add_game_address']);
                    }else{
                        $this->error('下载地址错误！');
                    }
                    break;
            }
        }else if($type==2){
            switch ($end_data['file_type']) {
                case ''://如果没设置原包
                    if(varify_url($end_data['ios_game_address'])){
                        Header("HTTP/1.1 303 See Other");
                        Header("Location: ".$end_data['ios_game_address']);
                    }else{
                        $this->error('下载地址错误！');
                    }
                break;
                case 2://如果设置了苹果包
                    if($end_data['ios_dow_address']!=''&&$end_data['ios_game_address']==""){
                        $this->down($end_data['ios_dow_address'],$type);
                    }else if(varify_url($end_data['ios_game_address'])){
                        Header("HTTP/1.1 303 See Other");
                        Header("Location: ".$end_data['ios_game_address']);
                    }else{
                        $this->error('下载地址错误！');
                    }
                break;
                default:
                    if(varify_url($end_data['ios_game_address'])){
                        Header("HTTP/1.1 303 See Other");
                        Header("Location: ".$end_data['ios_game_address']);
                    }else{
                        $this->error('下载地址错误！');
                    }
                    break;
            }
        }
        
        M('Game','tab_')->where('id='.$game_id)->setInc('dow_num');
        $this->add_down_stat($game_id);
    }

    function access_url($url) {    
        if ($url=='') return false;    
        $fp = fopen($url, 'r') or exit('Open url faild!');    
        if($fp){  
        while(!feof($fp)) {    
            $file.=fgets($fp)."";  
        }  
        fclose($fp);    
        }  
        return $file;  
    }  
    public function down($file, $type,$isLarge = false, $reload=ture,$rename = NULL)
    {
        if(headers_sent())return false;
        if(!$file&&$type==1) {
            $this->error('安卓文件不存在哦 亲!');
            //exit('Error 404:The file not found!');
        }
        if(!$file&&$type==2) {
            $this->error('苹果文件不存在哦 亲!');
            //exit('Error 404:The file not found!');
        }
        if(file_exists($file)){
            if($name==''){
                $name = basename($file);
            }
            $fp = fopen($file, 'rb+');
            $file_size = filesize($file);
            $ranges = $this->getRange($file_size);
            header('cache-control:public');
            header('Accenpt-Ranges: bytes');
            header('content-type:application/octet-stream');
            header('content-disposition:attachment; filename='.$name);
            if($reload && $ranges!=null){ // 使用断点下载，暂不能用
                header('HTTP/1.1 206 Partial Content');
                header('Accept-Ranges:bytes');
                // 剩余长度
                header(sprintf('content-length:%u',$ranges['end']-$ranges['start']));
                // range信息
                header(sprintf('content-range:bytes %s-%s/%s', $ranges['start'], $ranges['end'], $file_size));
                // fp指针跳到断点位置
                fseek($fp, sprintf('%u', $ranges['start']));
            }else{
                // header('HTTP/1.1 200 OK');//数据流下载
                // header('content-length:'.$file_size);
                // ob_clean();
                // flush();
                // readfile($file);
                header("Location:$file");//大文件下载
            }
            ($fp!=null) && fclose($fp);
        }else{
            $this->error('文件不存在哦 亲!');
        }
    }
    /** 获取header range信息
    * @param  int   $file_size 文件大小
    * @return Array
    */
    private function getRange($file_size){
        if(isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])){
            $range = $_SERVER['HTTP_RANGE'];
            $range = preg_replace('/[\s|,].*/', '', $range);
            $range = explode('-', substr($range, 6));
            if(count($range)<2){
                $range[1] = $file_size;
            }
            $range = array_combine(array('start','end'), $range);
            if(empty($range['start'])){
                $range['start'] = 0;
            }
            if(empty($range['end'])){
                $range['end'] = $file_size;
            }
            return $range;
        }
        return null;
    }
    /**
    *游戏下载统计
    */
    public function add_down_stat($game_id=null){
        $model = M('down_stat','tab_');
        $data['promote_id'] = 0;
        $data['game_id'] = $game_id;
        $data['number'] = 1;
        $data['type'] = 0;
        $data['create_time'] = NOW_TIME;
        $model->add($data);
    }
}
