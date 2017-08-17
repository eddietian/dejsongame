<?php
/**
 * 后台公共文件扩展
 * 主要定义后台公共函数库
 */
 
// lwx 

function seo_replace($str='',$array=array(),$site='media') {
    if ($site=='channel') {$title = C('CH_SET_TITLE');}
    else {$title = C('PC_SET_TITLE');}
    if (empty($str)) {return $title;}
    $find = array('%webname%','%gamename%','%newsname%','%giftname%','%gametype%');
    $replace = array($title,$array['game_name'],$array['title'],$array['giftbag_name'],$array['game_type_name']);
    $str =  str_replace($find,$replace,$str);
    
    return preg_replace('/((-|_)+)?((%[0-9A-Za-z_]*%)|%+)((-|_)+)?/','',$str);
}
 
 
 
// lwx 多选字段值判断
function check_field_value($field,$key) {
    if(empty($field) || empty($key)){
        return false;
    }
    $field = explode(",",$field);
    
    if (in_array($key,$field)) {
        return true;
    } else {
        return false;
    }
    
}

// lwx 获得所有开放类型
function get_opentype_all() {
    
    $list = M("Opentype","tab_")->where("status=1")->select();

    if (empty($list)) {return '';}

    return $list;
}
 
// lwx 获得所有游戏类型
function get_game_type_all() {

    $list = M("Game_type","tab_")->where("status_show=1")->select();

    if (empty($list)) {return '';}

    return $list;

}
function get_parent_id($id){
    $pdata=M('Promote','tab_')->where(array('id'=>$id))->find();
    if(empty($pdata)){
        return false;
    }else{
        $p_id=$pdata['parent_id'];
        return $p_id;
    }
}
// lwx 获得所有游戏
function get_game_id_all()

 {

    $game = M("game","tab_");

    $map['game_status'] = 1;

    $lists = $game->field("id,game_name")->where($map)->select();

    if(empty($lists)){return false;}

    return $lists;

 }

/**
 * 获取游戏列表
 * @return array，false
 * @author 小纯洁 
 */
 function get_game_list()
 {
    $game = M("game","tab_");
    $map['game_status'] = 1;
    $lists = $game->where($map)->select();
    if(empty($lists)){return false;}
    return $lists;
 }
 //合作模式
function get_pattern($type){
   if($type==0){
        return "CPS";
    }else{
        return "CPA";
    } 
}

/**
*游戏区服名称
*/
function get_area_name($area_id= null){
    if(empty($area_id)){return false;}
    $area_model = D('Server');
    $map['server_num'] = $area_id;
    $name = $area_model->where($map)->find();
    if(empty($name['server_name'])){return false;}
    return $name['server_name'];
}
/**
 * 获取对应游戏类型的文字信息
 */
function get_game_type($type = null){
    if(!isset($type)){
        return false;
    }
    $cl = M("game_type","tab_")->where("status=1 and id=$type")->limit(1)->select();
    return $cl[0]['type_name'];
}
/**
*获取推广员列表
*@return array
*@author 小纯洁
*/
 function get_promote_list(){
    $promote = M("promote","tab_");
    $map['status'] = 1;
    $data = $promote->where($map)->select();
    if(empty($data)){return false;}
    return $data;
 }
 
 /**
*检查链接地址是否有效
*/
function varify_url($url){  
    $check = @fopen($url,"r");  
    if($check){  
     $status = true;  
    }else{  
     $status = false;  
    }    
    return $status;  
} 

/**
获取推广员类型 一级 二级
*/
 function get_promote_type($id=0){
    $promote = M("Promote","tab_");
    $map["id"] = $id;
    $data = $promote->where($map)->find();
    if(empty($data)){return false;}
    $str="";
    switch ($data['parent_id']) {
        case 0:
            $str = "一级公会";
            break;
        
        default:
           $str = "二级公会";
            break;
    }
    
    return $str;
 }

 /**
*获取推广员账号
*@param  $promote_id 推广id
*@return string
*@author 小纯洁
*/
 function get_promote_name($prmote_id=0)
 {
    $promote = M("promote","tab_");
    $map['id'] = $prmote_id;
    $data = $promote->where($map)->find();
    if(empty($data)){return '自然注册';}
    if(empty($data['account'])){return "未知推广";}
    $result = $data['account'];
    return $result;
 }


/**
*获取推广员父类账号
*@param  $promote_id 推广id
*@param  $isShow bool 
*@return string
*@author 小纯洁
*/
 function get_parent_promote($prmote_id=0,$isShwo=true)
 {
    $promote = M("promote","tab_");
    $map['parent_id'] = $prmote_id;
    $data = $promote->where($map)->find();
    if(empty($data)){return false;}
    $result = "";
    if($isShwo){
        $result = "[{$data['account']}]";
    }
    else{
        $result = $data['account'];
    }
    return $result;
 }
/**
*获取推广员父类账号  改写
*@param  $promote_id 推广id
*@param  $isShow bool 
*@return string
*@author yyh
*/
function get_parent_promote_($prmote_id=0,$isShwo=true)
 {
    $promote = M("promote","tab_");
    $map['id'] = $prmote_id;//本推广员的id
    $data1 = $promote->where($map)->find();//本推广员的记录
    if(empty($data1)){return false;}
    if($data1['parent_id']==0){return false;}
    if($data1['parent_id']){
        $map1['id']=$data1['parent_id'];
    }
    $data = $promote->where($map1)->find();//父类的记录
    $result = "";
    if($isShwo){
        $result = "[{$data['account']}]";
    }
    else{
        $result = $data['account'];
    }
    return $result;
 }
/**
*获取推广员子账号
*/
 function get_prmoote_chlid_account($id=0){
    $promote = M("promote","tab_");
    $map['status'] = 1;
    $map["parent_id"] = $id;
    $data = $promote->where($map)->select();
    if(empty($data)){return "";}
    return $data;
 }

/**
*获取管理员昵称
*/
 function get_admin_name($id=0){
    if($id==null){
        $id=0;
    }
    $data = M("Member")->find($id);
    if(empty($data)){return "";}
    return $data['nickname'];
 }

 
 /**
 *获取用户实体
 */
 function get_user_entity($id=0,$isAccount = false){
    $user = M('user',"tab_");
    if($isAccount){
        $map['account'] = $id;
        $data = $user->where($map)->find();
    }
    else{
        $data = $user->find($id);
    }
    if(empty($data)){
        return false;
    }
    return $data;
 }

/**
*设置状态文本
*/
 function get_status_text($index=1,$mark=1){
    $data_text = array(
        0  => array( 0 => '失败' ,1 => '成功'),
        1  => array( 0 => '锁定' ,1 => '正常'),
        2  => array( 0 => '未申' ,1 => '已审' , 2 => '拉黑'),
        3  =>array(0=>'不参与',1=>'已参与'),
    );
    return $data_text[$index][$mark];
 }


/**
* 生成唯一的APPID
* @param  $str_key 加密key
* @return string
* @author 小纯洁 
*/
function generate_game_appid($str_key=""){
    $guid = '';  
    $data = $str_key;  
    $data .= $_SERVER ['REQUEST_TIME'];     
    $data .= $_SERVER ['HTTP_USER_AGENT']; 
    $data .= $_SERVER ['SERVER_ADDR'];       
    $data .= $_SERVER ['SERVER_PORT'];      
    $data .= $_SERVER ['REMOTE_ADDR'];     
    $data .= $_SERVER ['REMOTE_PORT'];     
    $hash = strtoupper ( hash ( 'MD4', $guid . md5 ( $data ) ) ); //ABCDEFZHIJKLMNOPQISTWARY
    $guid .= substr ( $hash, 0, 9 ) . substr ( $hash, 17, 8 ) ; 
    return $guid;
}


/**
*随机生成字符串
*@param  $len int 字符串长度
*@return string
*@author 小纯洁
*/
function sp_random_string($len = 6) {
    $chars = array(
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}
//随机数字
function sp_random_num($len = 6) {
    $chars = array(
         "0", "1", "2",
        "3", "4", "5", "6", "7", "8", "9"
    );
    $charsLen = count($chars) - 1;
    shuffle($chars);    // 将数组打乱
    $output = "";
    for ($i = 0; $i < $len; $i++) {
        $output .= $chars[mt_rand(0, $charsLen)];
    }
    return $output;
}
//判断支付设置
//yyh
function pay_set_status($type){
    $sta=M('tool','tab_')->field('status')->where(array('name'=>$type))->find();
    return $sta['status'];
}
//获取推广员父类id
function get_fu_id($id){
    $map['id']=$id;
    $pro=M("promote","tab_")->where($map)->find();
    if(null==$pro||$pro['parent_id']==0){
        return 0;
    }else{
    return $pro['parent_id'];
    }
}
function get_parent_name($id){
    $map['id']=$id;
    $pro=M("promote","tab_")->where($map)->find();
     if(null!=$pro&&$pro['parent_id']>0){
        $pro_map['id']=$pro['parent_id'];
        $pro_p=M("promote","tab_")->where($pro_map)->find();
        return $pro_p['account'];
     }else if($pro['parent_id']==0){
        return $pro['account'];
     }else{
        return false;
     }
}
//获取当前子渠道
function get_zi_promote_id($id){
    $map['parent_id']=$id;
    $pro=M("promote","tab_")->field('id')->where($map)->select();
    if(null==$pro){
        return 0;
    }else{
    for ($i=0; $i <count($pro); $i++) { 
        $sd[]=implode(",", $pro[$i]);
    }
    return  implode(",", $sd);    
    }
}
//检查同游戏其他版本是否存在  yyh
function checked_game($id,$sibling_id){
    if($sibling_id){
        $map['id']=array('neq',$id);
        $map['sibling_id']=$sibling_id;
        $game=M('Game','tab_')->where($map)->find();
        if(empty($game)){
            return '';
        }else{
            return $game;
        }
    }else{
        return false;
    }
}
//原包类型 yyh
function file_type($type=1){
    $type==1?$file_type='安卓':$file_type='苹果';
    return $file_type;
}
//把同游戏不同版本的下载地址合并到一起 yyh
function game_merge($data,$map){
    header("Content-type: text/html; charset=utf-8");
    // var_dump($data);exit;
    $model=M('Game','tab_');
    for ($i=0; $i <count($data); $i++) { 
        if($data[$i]['sdk_version']==0&&$data[$i]['sdk_version']!=''){
            $data[$i]['ios_id']=$data[$i]['id'];
        }else if($data[$i]['sdk_version']==1){
            $data[$i]['and_id']=$data[$i]['id'];
        }
        unset($data[$i]['id']);
        $sibling_id=$data[$i]['sibling_id'];
        $map['id']=array('neq',$sibling_id);
        $map['sibling_id']=$sibling_id;
        $game_data=$model->where($map)->find();
        if($game_data['sdk_version']==0&&$game_data['sdk_version']!=''){
            $data[$i]['ios_id']=$game_data['id'];
            $data[$i]['ios_dow_address']=$game_data['ios_dow_address'];
            $data[$i]['ios_game_address']=$game_data['ios_game_address'];
        }else if ($game_data['sdk_version']==1){
            $data[$i]['and_id']=$game_data['id'];
            $data[$i]['and_dow_address']=$game_data['and_dow_address'];
            $data[$i]['add_game_address']=$game_data['add_game_address'];
        }
    }
    return $data;
}

// //计算数组个数用于模板
function arr_count1($string){
    if($string){
        $arr=explode(',',$string);
        $cou=count($arr);
    }else{
        $cou=0;
    }
    return $cou;
}

/**
 * 判断手机访问型号
 * @return string
 */
function get_device_type()
{
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 'other';
    //分别进行判断
    if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
        $type = 'ios';
    }

    if (strpos($agent, 'android')) {
        $type = 'android';
    }
    return $type;
}

//查询uc用户是否存在该平台
function find_uc_account($name){
    $map['account']=trim($name);
    $user=M('user','tab_')->where($map)->find();
    if(null==$user){
        return false;
    }else{
        return $user;
    }   
}

//获取微信支付类型 0官方 1 威富通
function get_wx_type(){
    $map['name']=array('like','%wei%');
    $type=M('Tool','tab_')->where($map)->select();
    foreach ($type as $k => $v) {
    if($v['status']==1){
    $name=$v['name'];
    }
    }
   return $name=="weixin"?1:0;
}