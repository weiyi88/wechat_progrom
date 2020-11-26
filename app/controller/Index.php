<?php
namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        //获得参数 signature nonce token timestamp echostr
        $nonce=$_GET['nonce'];
        $token='Aring';
        $timestamp=$_GET['timestamp'];
        $signature=$_GET['signature'];
        $echostr=$_GET['echostr'];
        //形成数组，然后按字典排序
        $array=[];
        $array=array($nonce,$timestamp,$token);
        sort($array);
        //拼接成字符串，sha1加密，然后与signature进行校验
        $str=sha1(implode($array));
        if ($str==$signature && $echostr){
            //第一次接入微信api接口时候，验证通过之后会少穿echostr参数
            echo $echostr;
            exit;
        }else{
            $this->reponseMsg();
        }

    }

    /**
     * 接收时间推送并回复
     */
    public function reponseMsg(){

        //1,获取到微信推送过来post数据（xml格式）
        $postArr=$GLOBALS['HTTP_RAW_POST_DATA'];

        //2,处理消息类型，并且设置回复类型和内容

        //将xml=>object
        $postObj=simplexml_load_string($postArr);

        /*
         * $postObj内的属性
         * $postObj->ToUserName=''//开发者微信号
         * $postObj->FromUserName=''//用户openid
         * $postObj->CreateTime=''//创建时间
         * $postObj->MsgType=''//消息类型,event
         * $postObj->Event=''//时间类型，subscribe订阅，unsubscribe取消订阅两种
         * 判断该数据包是否是订阅的时间推送
         * */
        if ( strtolower($postObj->MsgType)=='event'){
            //如果是关注subscribe时间
            if ( strtolower($postObj->Event =='subscribe')){
                //回复用户信息
                $toUser=$postObj->FromUserName;
                $fromUser=$postObj->ToUserName;
                $time=time();
                $msgtype='text'; //回复格式
                $content='欢迎关注Aring 公众号';

                //回复格式
                $template=" <xml>
                  <ToUserName><![CDATA[%s]]></ToUserName>
                  <FromUserName><![CDATA[%s]]></FromUserName>
                  <CreateTime>%s</CreateTime>
                  <MsgType><![CDATA[%s]]></MsgType>
                  <Content><![CDATA[%s]]></Content>
                </xml>
                ";

                //sprintf  参数   1，模板    2-其他    对应的模板的%s变量文字
                $info=sprintf($template,$toUser,$fromUser,$time,$msgtype,$content);

                echo $info;
            }

        }


    }


}
