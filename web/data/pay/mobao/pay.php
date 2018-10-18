<?php
require_once("lib/MobaoPay.class.php");

class pay_mobao
{

// 商户APINAME，WEB渠道一般支付
    protected $mobaopay_apiname_pay = "WEB_PAY_B2C";
// 商户APINAME，商户订单信息查询
    protected $mobaopay_apiname_query = "MOBO_TRAN_QUERY";
// 商户APINAME，Mo宝支付退款申请
    protected $mobaopay_apiname_refund = "MOBO_TRAN_RETURN";
// 商户API版本
    protected $mobaopay_api_version = "1.0.0.0";
// Mo宝支付系统密钥
    protected $mbp_key = "4de149e8d905201341c1ab7a2c40def3";
// Mo宝支付系统网关地址（正式环境）
    protected $mobaopay_gateway = "https://trade.gannun.cn/cgi-bin/netpayment/pay_gate.cgi";
// 商户在Mo宝支付的平台号
    protected $platform_id = "210001330017225";
// Mo宝支付分配给商户的账号
    protected $merchant_acc = "210001330017225";


    public function __construct()
    {
        $this->_init();
    }

    public function pay($payment, $amount, $orderNo, $url_callback, $url_return)
    {
        $data = array();
// 商户APINMAE，WEB渠道一般支付
        $data['apiName'] = $this->mobaopay_apiname_pay;
// 商户API版本
        $data['apiVersion'] = $this->mobaopay_api_version;
// 商户在Mo宝支付的平台号
        $data['platformID'] = $this->platform_id;
// Mo宝支付分配给商户的账号
        $data['merchNo'] = $this->merchant_acc;
// 商户通知地址
        $data['merchUrl'] = $url_callback;

//商户订单号
        $data['orderNo'] = $orderNo;
// 商户订单日期
        $data['tradeDate'] = date("Ymd");
// 商户交易金额
        $data['amt'] = $amount;
// 商户参数
        $data['merchParam'] = $payment['id'];
// 商户交易摘要
        $data['tradeSummary'] = "用户商品购买";
// 对含有中文的参数进行UTF-8编码
// 将中文转换为UTF-8
        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['merchUrl'])) {
            $data['merchUrl'] = iconv("GBK", "UTF-8", $data['merchUrl']);
        }
        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['merchParam'])) {
            $data['merchParam'] = iconv("GBK", "UTF-8", $data['merchParam']);
        }
        if (!preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $data['tradeSummary'])) {
            $data['tradeSummary'] = iconv("GBK", "UTF-8", $data['tradeSummary']);
        }
        $cMbPay = new MbPay($this->mbp_key, $this->mobaopay_gateway);
        $str_to_sign = $cMbPay->prepareSign($data);
        $sign = $cMbPay->sign($str_to_sign);
        $data['signMsg'] = $sign;
        $form = $cMbPay->buildForm($data, $this->mobaopay_gateway);

        $json['pay_type'] = "FORM";
        $json['pay_act'] = "post";
        $json['pay_html'] = $form;
        return $json;
    }


    public function callback($sucFuc = null)
    {
        $data = "";
        $data['apiName'] = $_REQUEST["apiName"];
// 通知时间
        $data['notifyTime'] = $_REQUEST["notifyTime"];
// 支付金额(单位元，显示用)
        $data['tradeAmt'] = $_REQUEST["tradeAmt"];
// 商户号
        $data['merchNo'] = $_REQUEST["merchNo"];
// 商户参数，支付平台返回商户上传的参数，可以为空
        $data['merchParam'] = $_REQUEST["merchParam"];
// 商户订单号
        $data['orderNo'] = $_REQUEST["orderNo"];
// 商户订单日期
        $data['tradeDate'] = $_REQUEST["tradeDate"];
// Mo宝支付订单号
        $data['accNo'] = $_REQUEST["accNo"];
// Mo宝支付账务日期
        $data['accDate'] = $_REQUEST["accDate"];
// 订单状态，0-未支付，1-支付成功，2-失败，4-部分退款，5-退款，9-退款处理中
        $data['orderStatus'] = $_REQUEST["orderStatus"];
// 签名数据
        $data['signMsg'] = $_REQUEST["signMsg"];
//print_r( $data);
// 初始化
        $cMbPay = new MbPay($this->mbp_key, $this->mobaopay_gateway);
// 准备准备验签数据
        $str_to_sign = $cMbPay->prepareSign($data);
// 验证签名
        $resultVerify = $cMbPay->verify($str_to_sign, $data['signMsg']);
//var_dump($data);
        if ($resultVerify) {
            //异步通知
            if ('1' == $_REQUEST["notifyType"]) {
                call_user_func($sucFuc, $data);
                return true;
            }
            // 签名验证通过
            echo "支付成功" . '<br>';
            echo "商户订单号 " . $data['orderNo'] . '<br>';
            echo "商户订单日期 " . $data['tradeDate'] . '<br>';
            echo "商户参数 " . $data['merchParam'] . '<br>';
            echo "Mo宝支付订单号 " . $data['accNo'] . '<br>';
            echo "Mo宝支付账务日期 " . $data['accDate'] . '<br>';
            echo "支付金额 " . $data['tradeAmt'] . "元" . '<br>';
            echo "订单状态 ";

            if ($data['orderStatus'] == '0')
                echo "未处理[" . $data['orderStatus'] . "]";
            else if ($data['orderStatus'] == '1')// 需更新商户系统订单状态
                echo "成功[" . $data['orderStatus'] . "]";
            else if ($data['orderStatus'] == '2')// 需更新商户系统订单状态
                echo "失败[" . $data['orderStatus'] . "]";
            else if ($data['orderStatus'] == '4')// 需更新商户系统订单状态
                echo "部分退货[" . $data['orderStatus'] . "]";
            else if ($data['orderStatus'] == '5')// 需更新商户系统订单状态
                echo "全部退货[" . $data['orderStatus'] . "]";
            else if ($data['orderStatus'] == '9')// 需更新商户系统订单状态
                echo "退款处理中[" . $data['orderStatus'] . "]";
            else if ($data['orderStatus'] == '11')
                echo "订单过期[" . $data['orderStatus'] . "]";
            else
                echo "其他[" . $data['orderStatus'] . "]";

            /*商户需要在此处判定通知中的订单状态做后续处理*/
            /*由于页面跳转同步通知和异步通知均发到当前页面，所以此处还需要判定商户自己系统中的订单状态，避免重复处理。*/
            call_user_func($sucFuc, $data);
            return true;
        } else {
            // 签名验证失败
            echo "NO ACCESS";
            return false;
        }

    }

    private function _init()
    {

    }
}

?> 