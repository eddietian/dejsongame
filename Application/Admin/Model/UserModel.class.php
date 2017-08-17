<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 文档基础模型
 */
class UserModel extends Model{

    /* 自动验证规则 */
    protected $_validate = array(
        array('account', '', -3, self::EXISTS_VALIDATE, 'unique'), //用户名被占用
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'function', UC_AUTH_KEY),
        array('anti_addiction', 0, self::MODEL_INSERT),
        array('lock_status', 1, self::MODEL_INSERT),
        array('balance', 0, self::MODEL_INSERT),
        array('cumulative', 0, self::MODEL_INSERT),
        array('vip_level', 0, self::MODEL_INSERT),
        //array('register_ip', 'get_client_ip', self::MODEL_INSERT, 'function'),
        array('register_time', NOW_TIME,self::MODEL_INSERT),
    );

    //protected $this->$tablePrefix = 'tab_'; 
    /**
     * 构造函数
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        /* 设置默认的表前缀 */
        $this->tablePrefix ='tab_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }

    public function login($account,$password,$type,$game_id,$game_name){
        $map['account'] = $account;
        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if(is_array($user) && $user['lock_status']){
            /* 验证用户密码 */
            if(think_ucenter_md5($password, UC_AUTH_KEY) === $user['password'] || $type == 3){
                //动态密码
                if($user['otp_status'] == 1 && $type == 4){
                    if (empty(I('post.code'))) {
                        return -3;
                    } elseif (!R('Sdk/OTP/verifyKey', array($user['id'], I('post.code')))) {
                        return -4;
                    }
                }

                $this->updateLogin($user['id']); //更新用户登录信息
                $this->user_login_record($user,$type,$game_id,$game_name);
                return $user['id']; //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }
    //user表新加game_id 如果没有数据,新加数据
    public function login_($account,$password,$type,$game_id,$game_name,$sdk_version){
        $map['account'] = $account;
        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if(is_array($user) && $user['lock_status']){
            /* 验证用户密码 */
            if(think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']){
                $token = $this->updateLogin_($user['id'],$accunt,$password,$user['fgame_id'],$game_id,$game_name); //更新用户登录信息
                $this->user_login_record($user,$type,$game_id,$game_name,$sdk_version);
                return array("user_id"=>$user['id'],"token"=>$token); //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }
    //用户登录记录
    public function user_login_record($data,$type,$game_id,$game_name,$sdk_version){
        $data=array(
            'user_id'=>$data['id'],
            'user_account'=>$data['account'],
            'user_nickname'=>$data['nickname'],
            'game_id'=>$game_id,
            'game_name'=>$game_name,
            'server_id'=>null,
            'type'=>$type,
            'server_name'=>null,
            'login_time'=>NOW_TIME,
            'login_ip'=>get_client_ip(),
            'sdk_version'=>$sdk_version,
        );
            $uid =M('user_login_record','tab_')->add($data);
            return $uid ? $uid : 0; //0-未知错误，大于0登录记录成功
    }
    /**
    *游戏用户注册
    */
    public function register($account,$password,$register_way,$promote_id=0,$promote_account="",$phone=""){
        $data = array(
            'account'    => $account,
            'password'   => $password,
            'nickname'   => $account,
            'phone'      => $phone,
            'promote_id' => $promote_id,
            'promote_account' => $promote_account,
            'register_way' => $register_way,
            'register_ip'  => get_client_ip(),
            'parent_id'=>get_fu_id($promote_id),
        );
        //if(!$this->checkAccount($account)){return -1;}
        /* 添加用户 */
        if($this->create($data)){
            $uid = $this->add();
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }

    /**
    *微信用户登录/注册
    */
    public function wx_register($userinfo){
        $data = array(
            'account'    => $userinfo['account'],
            'password'   => $userinfo['account'],
            'nickname'   => $userinfo['nickname'],
            'phone'      => $userinfo['phone'],
            'promote_id' => $userinfo['promote_id'],
            'promote_account' => $userinfo['promote_account'],
            'register_way' => $userinfo['register_way'],
            'register_ip'  => get_client_ip(),
            'parent_id'=>get_fu_id($userinfo['promote_id']),
            'parent_name'=>get_parent_name($userinfo['$promote_id']),
            'fgame_id'  =>$userinfo['game_id'],
            'fgame_name'=>$userinfo['game_name'],
            'sdk_version'=>$userinfo['sdk_version'],
            'openid'    =>$userinfo['openid'],
        );
        /* 添加用户 */
        if($this->create($data)){
            $uid = $this->add();
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }

    /**
    *游戏用户注册
    *user表加game_id
    */
    public function register_($account,$password,$register_way,$promote_id=0,$promote_account="",$phone="",$game_id="",$game_name="",$sdk_version=""){
        $data = array(
            'account'    => $account,
            'password'   => $password,
            'nickname'   => $account,
            'phone'      => $phone,
            'promote_id' => $promote_id,
            'promote_account' => $promote_account,
            'register_way' => $register_way,
            'register_ip'  => get_client_ip(),
            'parent_id'=>get_fu_id($promote_id),
            'parent_name'=>get_parent_name($promote_id),
            'fgame_id'  =>$game_id,
            'fgame_name'=>$game_name,
            'sdk_version'=>$sdk_version,
        );

        //if(!$this->checkAccount($account)){return -1;}
        /* 添加用户 */
        if($this->create($data)){
            $uid = $this->add();
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }
    /**
    *app用户注册
    */
    public function app_register($account,$password,$register_way,$nickname,$sex){
        $data = array(
            'account'    => $account,
            'password'   => $password,
            'register_way' => $register_way,            
            'nickname'   => $nickname,
            'sex' => $sex,
            'login_time'=>time(),
            'login_ip'=>get_client_ip(),
            'phone' => $account,
            'register_ip'  => get_client_ip(),
        );

        //if(!$this->checkAccount($account)){return -1;}
        /* 添加用户 */
        if($this->create($data)){
            $uid = $this->add();
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }
    /**
    *修改用户信息
    */
    public function updateUser($data){
        $c_data = $this->create($data);
        if(empty($data['password'])){
            unset($c_data['password']);
        }
        else {
            if(!$this->verifyUser($data['id'],$data['old_password'])){
               return -2;
            }
        }
        return  $this->where("id=".$data['id'])->save($c_data);
    }

    /**
     * 获取详情页数据
     * @param  integer $id 文档ID
     * @return array       详细数据
     */
    public function detail($id){
        /* 获取基础数据 */
        $info = $this->field(true)->find($id);
        if(!(is_array($info) || 1 !== $info['status'])){
            $this->error = '文档被禁用或已删除！';
            return false;
        }

        /* 获取模型数据 */
        $logic  = $this->logic($info['model_id']);
        $detail = $logic->detail($id); //获取指定ID的数据
        if(!$detail){
            $this->error = $logic->getError();
            return false;
        }
        $info = array_merge($info, $detail);

        return $info;
    }

    /**
    *检查账号是否存在
    */
    protected function checkAccount($account){
        $map['account'] = $account;
        $data = $this->where($map)->find();
        if(empty($data)){return true;}
        return false;
    }
    public function checkField($field, $type = 1){
        $data = array();
        switch ($type) {
            case 1:
                $data['account'] = $field;
                break;
            case 2:
                $data['email'] = $field;
                break;
            case 3:
                $data['phone'] = $field;
                break;
            default:
                return 0; //参数错误
        }

        return $this->create($data) ? 1 : $this->getError();
    }
    
    // 检查用户 lwx
    public function checkUsername($account){
        $map['account'] = $account;
        $data = $this->where($map)->find();
        if(empty($data)){return true;}
        return false;
    }
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['phone'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
    // 更改密码  lwx 2015-05-20
    public function updatePassword($id,$password) {
        $data['id']=$id;
        $data['password']=think_ucenter_md5($password, UC_AUTH_KEY);
        $find=$this->find($id);
        if($find['password']==$data['password']){
            return true;
        }else{
        $return = $this->save($data);
        if ($return == true){
            return true;
        }
        else {
            return false;
        }

        }
    }
    
    public function checkPassword($account,$password) {
        $map['account'] = $account;
        $map['password'] = think_ucenter_md5($password, UC_AUTH_KEY);
        $user = $this->where($map)->find();
        if(is_array($user) && $user['lock_status']){
            return true;
        } else {
            return false; 
        }
    }
    protected function updateLogin($uid){
        $model = M('User','tab_');
        $data["id"] = $uid;
        $data["login_time"] = NOW_TIME;
        $data["login_ip"] = get_client_ip();
        $model->save($data);
    }
    //判断game_id是否有值
    protected function updateLogin_($uid,$account,$password,$user_fgame_id,$game_id,$game_name){
        $model = M('User','tab_');
        $data["id"] = $uid;
        $data["login_time"] = NOW_TIME;
        $data["login_ip"] = get_client_ip();
        $data["token"] = $this->generateToken($uid,$account,$password);
        if($user_fgame_id){
            $model->save($data);
        }else{
            $data['fgame_id']=$game_id;
            $data['fgame_name']=$game_name;
            $model->save($data);
        }
        return $data["token"];
    }
    /**
    *随机生成token
    */
    protected function generateToken($user_id,$account,$password){
        $str = $user_id.$account.$password.NOW_TIME.sp_random_string(7);
        $token = MD5($str);
        return $token;
    }
    /**
    *更新玩家信息
    */
    public function updateInfo($data){
        $new_data = $this->create($data);
        if(empty($data['password'])){unset($new_data['password']);}
        $return = $this->save($new_data);
        return $return;
    }

    /**
     * 验证用户密码
     * @param int $uid 用户id
     * @param string $password_in 密码
     * @return true 验证成功，false 验证失败
     * @author huajie <banhuajie@163.com>
     */
    protected function verifyUser($uid, $password_in){
        $password = $this->getFieldById($uid, 'password');
        if(think_ucenter_md5($password_in, UC_AUTH_KEY) === $password){
            return true;
        }
        return false;
    }

    /**
     * 创建时间不写则取当前时间
     * @return int 时间戳
     * @author huajie <banhuajie@163.com>
     */
    protected function getCreateTime(){
        $create_time    =   I('post.create_time');
        return $create_time?strtotime($create_time):NOW_TIME;
    }


    /**
     * 生成不重复的name标识
     * @author huajie <banhuajie@163.com>
     */
    private function generateName(){
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';	//源字符串
        $min = 10;
        $max = 39;
        $name = false;
        while (true){
            $length = rand($min, $max);	//生成的标识长度
            $name = substr(str_shuffle(substr($str,0,26)), 0, 1);	//第一个字母
            $name .= substr(str_shuffle($str), 0, $length);
            //检查是否已存在
            $res = $this->getFieldByName($name, 'id');
            if(!$res){
                break;
            }
        }
        return $name;
    }
}