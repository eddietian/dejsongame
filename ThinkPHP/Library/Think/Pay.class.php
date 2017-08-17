<?php

/**
 * 通用支付接口类
 * @author yunwuxin<448901948@qq.com>
 */

namespace Think;
class Pay {

    /**
     * 支付驱动实例
     * @var Object
     */
    private $payer;

    /**
     * 配置参数
     * @var type 
     */
    private $config;

    /**
     * 构造方法，用于构造上传实例
     * @param string $driver 要使用的支付驱动
     * @param array  $config 配置
     */
    public function __construct($driver, $config = array()) {
        /* 配置 */
        $pos = strrpos($driver, '\\');
        $pos = $pos === false ? 0 : $pos + 1;
        $apitype = strtolower(substr($driver, $pos));
        $this->config['notify_url'] = 'http://'.$_SERVER ['HTTP_HOST']."/callback.php/Notify/notify/apitype/".$apitype.'/method/notify';
        $this->config['return_url'] = 'http://'.$_SERVER ['HTTP_HOST']."/callback.php/Notify/notify/apitype/".$apitype.'/method/return';
        $config = array_merge($this->config, $config);
        /* 设置支付驱动 */
        $class = strpos($driver, '\\') ? $driver : 'Think\\Pay\\Driver\\' . ucfirst(strtolower($driver));
        $this->setDriver($class, $config);
    }

    public function buildRequestForm(Pay\PayVo $vo) {
        $this->payer->check();
        $result = false;
        switch ($vo->getTable()) {
            case 'spend':
                $result = $this->add_spend($vo);
                break;
            case 'deposit':
                $result = $this->add_deposit($vo);
                break;
            case 'agent':
                $result = $this->add_agent($vo);
                break;
            default:
                $result = false;
                break;
        }
        if($result !== false) {//$check !== false
            return $this->payer->buildRequestForm($vo);
        } else {
            E(M($vo->getTable(),"tab_")->getDbError());
        }
    }

    /**
    *消费表添加数据
    */
    private function add_spend(Pay\PayVo $vo){
        $spend = M("spend","tab_");
        $spend_data['user_id']          = $vo->getUserId();
        $spend_data['user_account']     = $vo->getAccount();
        $spend_data['user_nickname']    = $vo->getUserNickName();
        $spend_data['game_id']          = $vo->getGameId();
        $spend_data['game_appid']       = $vo->getGameAppid();
        $spend_data['game_name']        = $vo->getGameName();
        $spend_data['server_id']        = $vo->getServerId();
        $spend_data['server_name']      = $vo->getServerName();
        $spend_data['promote_id']       = $vo->getPromoteId();
        $spend_data['promote_account']  = $vo->getPromoteName();
        $spend_data['order_number']     = "";
        $spend_data['pay_order_number'] = $vo->getOrderNo();
        $spend_data['props_name']       = $vo->getTitle();
        $spend_data['pay_amount']       = $vo->getFee();
        $spend_data['pay_way']          = $vo->getPayWay();
        $spend_data['pay_time']         = NOW_TIME;
        $spend_data['pay_status']       = 0;
        $spend_data['pay_game_status']  = 0;
        $spend_data['extend']           = $vo->getExtend();
        $spend_data['spend_ip']         = get_client_ip();
        $spend_data['sdk_version']      = $vo->getSdkVersion();
        $result = $spend->add($spend_data);
        return $result;
    }

    /**
    *平台币充值记录
    */
    private function add_deposit(Pay\PayVo $vo){
        $deposit = M("deposit","tab_");
        // $ordercheck = $deposit->where(array('pay_order_number'=>$data["order_no"]))->find();
        // if($ordercheck)$this->error("订单已经存在，请刷新充值页面重新下单！");
        $deposit_data['order_number']     = "";
        $deposit_data['pay_order_number'] = $vo->getOrderNo();
        $deposit_data['user_id']          = $vo->getUserId();
        $deposit_data['user_account']     = $vo->getAccount();
        $deposit_data['user_nickname']    = $vo->getUserNickName();
        $deposit_data['promote_id']       = $vo->getPromoteId();
        $deposit_data['promote_account']  = $vo->getPromoteName();
        $deposit_data['pay_amount']       = $vo->getFee();
        $deposit_data['reality_amount']   = $vo->getFee();
        $deposit_data['pay_status']       = 0;
        $deposit_data['pay_way']          = $vo->getPayWay();
        $deposit_data['pay_source']       = 0;
        $deposit_data['pay_ip']           = get_client_ip();
        $deposit_data['pay_source']       = 0;
        $deposit_data['create_time']      = NOW_TIME;
        $deposit_data['sdk_version']       = $vo->getSdkVersion();
        $result = $deposit->add($deposit_data);
        return $result;
    }

    /**
    *添加代充记录
    */
    private function add_agent(Pay\PayVo $vo){
        $agent = M("agent","tab_");
        $agnet_data['order_number']     = "";
        $agnet_data['pay_order_number'] = $vo->getOrderNo();
        $agnet_data['game_id']          = $vo->getGameId();
        $agnet_data['game_appid']       = $vo->getGameAppid();
        $agnet_data['game_name']        = $vo->getGameName();
        $agnet_data['promote_id']       = $vo->getPromoteId();
        $agnet_data['promote_account']  = $vo->getPromoteName();
        $agnet_data['user_id']          = $vo->getUserId();
        $agnet_data['user_account']     = $vo->getAccount();
        $agnet_data['user_nickname']    = $vo->getUserNickName();
        $agnet_data['pay_type']         = 0;//代充 转移
        $agnet_data['amount']           = $vo->getMoney();
        $agnet_data['real_amount']      = $vo->getFee();
        $agnet_data['pay_status']       = 0;
        $agnet_data['pay_way']          = $vo->getPayWay();
        $agnet_data['create_time']      = time();
        $agnet_data['zhekou']           = $vo->getParam();
        $agnet_data['sdk_version']       = $vo->getSdkVersion();
        $agent->create($agnet_data);
        $result = $agent->add();
        return $result;
    }

    /**
     * 设置支付驱动
     * @param string $class 驱动类名称
     */
    private function setDriver($class, $config) {
        $this->payer = new $class($config);
        if (!$this->payer) {
            E("不存在支付驱动：{$class}");
        }
    }

    public function __call($method, $arguments) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array(&$this, $method), $arguments);
        } elseif (!empty($this->payer) && $this->payer instanceof Pay\Pay && method_exists($this->payer, $method)) {
            return call_user_func_array(array(&$this->payer, $method), $arguments);
        }
    }

}
