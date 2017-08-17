<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 18:17
 */
namespace Admin\Controller;

class QuestionController extends ThinkController{

    public function lists($p=0){
        $page = intval($p);
        $page = $page ? $page : 1; //默认显示第一页数据
        $row = 10;
        $asd = I('get.account');
        empty($asd) || $map['account'] = array('like',"%".I('get.account')."%");
        $map['status'] = array('neq','-1');
        $data = M('question','tab_')->where($map)->page($page,$row)->order('create_time desc,id')->select();
        foreach ($data as $key => $value) {
            $data[$key]['question'] = json_decode($value['question'],true);
            $data[$key]['answer'] = json_decode($value['answer'],true);
        }
        $count = M('question','tab_')->where($map)->count();
        if($count > $row){
            $page = new \Think\Page($count, $row);
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
            $this->assign('_page', $page->show());
        }
        $this->assign('list_data',$data);
        $this->meta_title="用户留言列表";
        $this->display();
    }

    public function show($id){
        $data = M('question','tab_')->find($id);
        $question = json_decode($data['question'],true);
        foreach ($question as $k=>$v) {
            $content[$k][1] = $v;
        }
        $answer = json_decode($data['answer'],true);
        foreach ($answer as $key=>$value) {
            $content[$key][2] = $value;
        }
        ksort($content);
        $this->assign('user',$data);
        $this->assign('content',$content);
        $this->display();
    }

    public function reply(){
        !empty(I('reply')) || $this->error("回复内容不能为空");
        $data = M('question','tab_')->find(I('id'));
        $answer = json_decode($data['answer'],true);
        $answer[time()] = I('reply');
        $data['answer'] = json_encode($answer);
        $res = M('question','tab_')->save($data);
        if($res > 0){
            $this->success("回复成功",U('lists'));
        }else{
            $this->error("回复失败");
        }
    }

}