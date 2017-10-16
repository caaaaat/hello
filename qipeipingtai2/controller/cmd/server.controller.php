<?php

/**
 *
 * Class CmdServerController
 */
class CmdServerController extends Controller
{
    /**
     * 给客户发送客户通知微信
     */
    public function sendServerInfo()
    {
        $day = date("Y-m-d");
        $mo = model('pro.pay');
        $sendMo = model('msg');

        //取得指定日期的前一天出发的用户
        $rows = $mo->getStartBeforeUsers($day);
        if (!empty($rows)) {
            $tplId = '5MNQmOL089JQWTb2fv6sIsvMmKVxCO8uV2yroNjXQL0';
            //$tplId = '-C-Zz_2dHum5RLxj-CXIKaqlDNvBr6oRjyRjPJIn3m4';
            foreach ($rows as $item) {
                $sendTitle = '尊敬的';
                $name = $item['realName'] ? $item['realName'] : $item['nickName'];
                $sendTitle.= "" . $name . "，您的旅行\"" . $item['proName'] . "\"将于明天[" . date("m.d", strtotime($item['startDay'])) . "]出发";

                $sendFooter = "如未收到出团通知书或未与导游取得联系，请电话028-86245203获取帮助。\n";
                $sendFooter.= "请携带上衣物、洗漱用具、证件等必要出行物品；我们会在您旅游期间的每晚20点以微信的形式向您发送当日旅游服务点评信息，请您为当日的旅游服务进行评价，如您对当日的服务有任何不满我们将尽快为您解决。\n";
                $sendFooter.= "祝您旅行愉快！";
                $to  = $item['wxOpenId'];
                $url = '';
                $billCoder = $item['orderCoder'];
                $proName   = $item['proName'];
                $data = array(
                    'first' => $sendTitle,
                    'keyword1' => $billCoder,
                    'keyword2' => $proName,
                    'keyword3' => date("Y年m月d日",strtotime($item['startDay']))."",
                    'remark' => $sendFooter
                );
                if ($to) {
                    $rst = $sendMo->sendWxTplMsg($to, $tplId, $data, $url);
                    dump($rst);
                }
            }
        }


        //取得今天正在行程中的用户
        $rows = $mo->getPlayUsers($day);
        if (!empty($rows)) {
            $tplId = 'zj1awozZjfhZYt74QcHVCpPxIy5_J1WGzK1Bm-j_Oi8';
            foreach ($rows as $item) {
                $sendTitle = '尊敬的';
                $name = $item['realName'] ? $item['realName'] : $item['nickName'];
                $sendTitle .= "" . $name . ",今天的行程快结束了，邮电旅游邀请您对今日旅程中我们的服务进行评价，如有服务不好的地方，明天我们即刻改进...";

                $sendFooter = "》》点击进入点评《《";
                $to = $item['wxOpenId'];
                $url = 'http://pro.scydgl.com?m=plat.userServer.comment&a=comment&serverUserId='.$item['id'].'&day='.$day;
                $data = array(
                    'first' => $sendTitle,
                    'keyword1' => '订单号(' . $item['orderCoder'] . ')',
                    'keyword2' => date("Y-m-d"),
                    'remark' => $sendFooter
                );
                if ($to) {
                    $rst = $sendMo->sendWxTplMsg($to, $tplId, $data, $url);
                    dump($rst);
                }
            }
        }
    }
}