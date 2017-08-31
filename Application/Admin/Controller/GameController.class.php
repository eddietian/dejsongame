<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class GameController extends ThinkController {
    //private $table_name="Game";
    const model_name = 'game';

    /**
    *游戏信息列表
    */
     public function lists(){
        if(isset($_REQUEST['game_name'])){
            if($_REQUEST['game_name']=='全部'){
                unset($_REQUEST['game_name']);
            }else{
                $extend['game_name'] = $_REQUEST['game_name'];
                unset($_REQUEST['game_name']);
            }
        }
        if(isset($_REQUEST['game_type_name'])){
            if($_REQUEST['game_type_name']=='全部'){
                unset($_REQUEST['game_type_name']);
            }else{
                $extend['game_type_name'] = $_REQUEST['game_type_name'];
                unset($_REQUEST['game_type_name']);
            }
        }
        
        if(isset($_REQUEST['game_appid'])){
            $extend['game_appid'] = array('like','%'.$_REQUEST['game_appid'].'%');
            unset($_REQUEST['game_appid']);
        }
        parent::lists(self::model_name,$_GET["p"],$extend);
    }


    /**
    *游戏原包列表
    */
    public function source(){
        $extend = array('field_time'=>'create_time');
        parent::lists('Source',$_GET["p"],$extend);
    }

    /**
    *游戏更新列表
    */
    public function update(){
        parent::lists('Update',$_GET["p"]);
    }

    /**
    *添加游戏原包
    */
    public function add_source(){
        if(IS_POST){
            if(empty($_POST['game_id']) || empty($_POST['file_type'])){
                $this->error('游戏名称或类型不能为空');
            }
            $map['game_id']=$_POST['game_id'];
            $map['file_type'] = $_POST['file_type'];
            $d = D('Source')->where($map)->find();
            $source = A('Source','Event');
            if(empty($d)){
                $source->add_source();
            }
            else{
                $source->update_source($d['id']);
            }
        }
        else{

            $this->display();
        }
    }

    public function indexnav(){
        $this->meta_title = '首页导航';

        $data = array();
        $data['title'] = "导航";

        $this->assign("data", $data);
        $this->display();
    }

    /**
    *删除原包
    */
    public function del_game_source($model = null, $ids=null){
        $source = D("Source");
        $id = array_unique((array)$ids);
        $map = array('id' => array('in', $id) );
        $list = $source->where($map)->select();
        foreach ($list as $key => $value) {
            $file_url = APP_ROOT.$value['file_url'];
            unlink($file_url);
        }
        $model = M('Model')->getByName("source"); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids,"tab_game_");
    }

    public function add(){
    	if(IS_POST){
            $_POST['game_name']=str_replace(' ','',$_POST['game_name']);
            $_POST['game_name']=$_POST['game_name'].$_POST['g_version'];
            $_POST['g_version']=='(安卓版)'?$_POST['sdk_version']=1:$_POST['sdk_version']=0;
            unset($_POST['g_version']);
    		$game   =   D(self::model_name);//M('$this->$model_name','tab_');
            // $_POST['ratio']=$_POST['ratio']/100;
	        $res = $game->update();  
	        if(!$res){
	            $this->error($game->getError());
	        }else{
	            $this->success($res['id']?'更新成功':'新增成功',U('lists'));
	        }
    	}else{
            $add_another=array();
            $add_another=I();
            $add_another['game_sible']=='(安卓版)'?$add_another['game_sible']='(苹果版)':$add_another['game_sible']='(安卓版)';
            $this->assign('add_another',$add_another);  
         
            $sub=array(); 
            $sub['id']=$add_another['id']; 
            $list = D('Game')->where($sub)->field("sort,short,game_type_id,game_type_name,marking,game_score,features,recommend_level,version,icon,cover,screenshot,introduction,game_address,dow_num,game_status,recommend_status,pay_status,dow_status,developers,create_time,discount,language,game_appid,game_coin_name,game_coin_ration,category,ratio,money,sibling_id,sdk_version")->find();
            $this->assign('list',$list); 
            
            $this->meta_title = '新增游戏';
            $this->display();
    	}
    }

    public function edit($id=null){
        if(IS_POST){
            $game   =   D(self::model_name);//M('$this->$model_name','tab_');
            $res = $game->update();   
            
            $id=$res["id"];
            $sibling=D("Game")->find($id);               //获取所有
            
            $sibling_id=$sibling["sibling_id"];
            $map['sibling_id'] = $sibling_id;
            $sid=$sibling['id'];
            $map['id'] = array('neq',$sid);
            $another=D("Game")->where($map)->find();  //获取另一个所有
            
            $phone['id'] = $another['id'];
            $phone['game_type_id'] =$sibling['game_type_id'];
            $phone['game_type_name'] =$sibling['game_type_name'];
            $phone['category']=$sibling['category'] ;
            $phone['sort']= $sibling['sort'];
            $phone['icon']= $sibling['icon'];
            $phone['cover']= $sibling['cover'];
            $phone['features']= $sibling['features'];
            $phone['marking']= $sibling['marking'];
            $phone['game_score']=$sibling['game_score'] ;
            $phone['recommend_level']=$sibling['recommend_level'] ;
            $phone['game_status']= $sibling['game_status'];
            $phone['recommend_status']= $sibling['recommend_status'];
            $phone['dow_status']=$sibling['dow_status']; 
            M('Game','tab_')->data($phone)->save();
            
            if(!$res){
                $this->error($game->getError());
            }else{
                $this->success($res['id']?'更新成功':'新增成功',U('lists'));
            }
        }
        else{
            $id || $this->error('id不能为空');
            $data = D(self::model_name)->detailback($id);
            $data || $this->error('数据不存在！');
            if(!empty($data['and_dow_address'])){
                $data['and_dow_address']= ltrim($data['and_dow_address'],'.');
            }
            if(!empty($data['ios_dow_address'])){
                $data['ios_dow_address']=ltrim($data['ios_dow_address'],'.');
            }
            $this->assign('data', $data);
            $this->meta_title = '编辑游戏';
            $this->display();
        }
    }

    public function set_status($model='Game'){
        parent::set_status($model);
    }

    public function del($model = null, $ids=null){
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::remove($model["id"],'Set',$ids);
    }
    //开放类型
    public function openlist(){
        $extend = array(
        );
        parent::lists("opentype",$_GET["p"],$extend);
    }
    //新增开放类型
    public function addopen(){
        if(IS_POST){
            $game=D("opentype");
        if($game->create()&&$game->add()){
            $this->success("添加成功",U('openlist'));
        }else{
            $this->error("添加失败",U('openlist'));
        }
        }else{
            $this->display();
        }
        
    }
    //编辑开放类型
    public function editopen($ids=null){
          $game=D("opentype");
        if(IS_POST){
        if($game->create()&&$game->save()){
             $this->success("修改成功",U('openlist'));
        }else{
           $this->error("修改失败",U('openlist'));
        }
        }else{  
         $map['id']=$ids;
            $date=$game->where($map)->find();
            $this->assign("data",$date);
            $this->display();
        }
    }
    //删除开放类型
    public function delopen($model = null, $ids=null){
       $model = M('Model')->getByName("opentype"); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids);
    }
    /**
     * 文档排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        //获取左边菜单$this->getMenus()
       
        if(IS_GET){
            $map['status'] = 1;
            $list = D('Game')->where($map)->field('id,game_name')->order('sort DESC, id DESC')->select();

            $this->assign('list', $list);
            $this->meta_title = '游戏排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = array_reverse(explode(',', $ids));
            foreach ($ids as $key=>$value){
                $res = D('Game')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！');
            }else{
                $this->error('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }

}
