<?php
include('Des3.class.php');

class pay_xunftong
{
    protected $des;

    public $errMsg = '';

    protected $config = array(
        'merNo' => 'Mer201703102274',
        'signKey' => '06F319B44177EA2D538EC887FBF7A02B',
        'encKey' => 'DxyZgPzNUbFzJqDKxwrmyYrE',
        "wechat_pay" => 'http://wx.h8pay.com/api/pay.action',
        "alipay_pay" => 'http://zfb.h8pay.com/api/pay.action',
        "alipay_wap_pay" => 'http://zfbwap.h8pay.com/api/pay.action',
    );

    private function _init()
    {
        $this->des = new DES3($this->config['encKey']);
    }

    public function __construct()
    {
        $this->_init();
    }

    public function pay($payment, $amount, $orderNo, $url_callback, $url_return)
    {
        $payTmp = json_decode($payment["config"], true);
        $pay = array();
        $pay['merNo'] = $this->config['merNo']; #商户号
        $pay['netway'] = $payTmp["netway"];  #WX 或者 ZFB
        $pay['random'] = (string)rand(1000, 9999);  #4位随机数    必须是文本型
        $pay['orderNum'] = $orderNo;  #商户订单号
        $pay['amount'] = (string)($amount * 100);//转换成元需要 * 100   必须是文本型
        $pay['goodsName'] = '商品支付';  #商品名称
        $pay['callBackUrl'] = $url_callback;  #通知地址 可以写成固定
        $pay['callBackViewUrl'] = $url_return;  #前台跳转 可以写成固定
        ksort($pay); #排列数组 将数组已a-z排序
        $sign = md5($this->json_encode($pay) . $this->config['signKey']); #生成签名
        $pay['sign'] = strtoupper($sign); #设置签名
        $data = $this->json_encode($pay); #将数组转换为JSON格式
        $post = array('data' => $data);
        $return = $this->wx_post($this->config[$payTmp["pay_type"]], $post); #提交订单数据
        $row = json_decode($return, true); #将返回json数据转换为数组
        if ($row['stateCode'] !== '00') {
            $this->errMsg = $row['msg'];
            return false;
        }
        if ($this->is_sign($row, $this->config['signKey'])) {
            #验证返回签名数据
            $json["orderNum"] = $row['orderNum'];
            $json["msg"] = $row['msg'];
            $json['pay_type'] = "ONLINE_QRCODE";
            $json['pay_act'] = "post";
            $json['pay_code'] = $row['qrcodeUrl'];
            return $json;
        }
        return false;
    }

    //回调
    public function callback($sucFuc = null)
    {
        $data = $_REQUEST['data'];
        $arr = json_decode($data, true);
        if ($this->is_sign($arr, $this->config['signKey'])) {
            $amount = (int)$arr['amount'];
            $amount = $amount / 100;
            $goodsName = $arr['goodsName'];
            $orderNum = $arr['orderNum'];
            $payDate = $arr['payDate'];
            $payResult = $arr['payResult'];
            file_put_contents(SYSTEM . "/data/log/a.log", $this->json_encode($arr));
            if ($payResult == '00') {
                $payRet = array();
                $payRet["orderNo"] = $orderNum;
                $payRet["tradeAmt"] = $amount;
                call_user_func($sucFuc, $payRet);
                echo '0'; #数据验证完成必须输出0告诉系统 通知完成。
                exit();
            }
        } else {
            echo "NO ACCESS";
            return false;
        }
    }

    private function is_sign($row, $signKey)
    { #效验服务器返回数据
        $r_sign = $row['sign']; #保留签名数据
        $arr = array();
        foreach ($row as $key => $v) {
            if ($key !== 'sign') { #删除签名
                $arr[$key] = $v;
            }
        }
        ksort($arr);
        $sign = strtoupper(md5($this->json_encode($arr) . $signKey)); #生成签名
        if ($sign == $r_sign) {
            return true;
        } else {
            return false;
        }

    }

    private function json_encode($input)
    {
        if (is_string($input)) {
            $text = $input;
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(
                array("\r", "\n", "\t", "\""),
                array('\r', '\n', '\t', '\\"'),
                $text);
            return '"' . $text . '"';
        } else if (is_array($input) || is_object($input)) {
            $arr = array();
            $is_obj = is_object($input) || (array_keys($input) !== range(0, count($input) - 1));
            foreach ($input as $k => $v) {
                if ($is_obj) {
                    $arr[] = self::json_encode($k) . ':' . self::json_encode($v);
                } else {
                    $arr[] = self::json_encode($v);
                }
            }
            if ($is_obj) {
                return '{' . join(',', $arr) . '}';
            } else {
                return '[' . join(',', $arr) . ']';
            }
        } else {
            return $input . '';
        }
    }

    private function wx_post($url, $data)
    { #POST访问
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        return $tmpInfo;
    }

}

?> 