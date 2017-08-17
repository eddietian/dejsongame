<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 18:17
 */
namespace Admin\Controller;

class MsgController extends ThinkController{

    public function lists($p=0){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row = 10;

        $map['user_id'] = session('user_auth.uid');
        $map['status'] = array('neq','-1');
        $data = D('Msg')->where($map)->page($page,$row)->order('status desc,id')->select();
        $count = D('Msg')->where($map)->count();

        if(isset($_REQUEST['content'])){
            $map['content']=array('like','%'.$_REQUEST['content'].'%');
        }
        $data = D('Msg')->where($map)->page($page,$row)->order('status desc,id')->select();
        
        if($count > 10){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data',$data);
        $this->meta_title = ' 站内信';
        $this->display();
    }

    public function read($ids=0){
        if (!empty($ids)) {
            $map['id'] = array('in', $ids);
            $res = M('Msg', 'tab_')->where($map)->setField(array('status' => 1));
        }
        if($res == 1){
            redirect($_SERVER["HTTP_REFERER"]);
        }else{
            $this->success();
        }
//        header("location:".$_SERVER["HTTP_REFERER"]);
    }

}