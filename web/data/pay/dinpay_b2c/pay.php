<?php

//https://merchants.dinpay.com/merchantUserLogin
//账号：admin724
//商户号：2130000234
//密码：aa123321
//B2C支付

class pay_dinpay_b2c
{
    private $payment_id = 10;

    //商户私钥
    private $merchant_private_key = "-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBALRU7RTkZ9yEPQi/
fCONFcF4TCV921HBCCeM4h+QEfxyhciidlM/QfFlAwJtkT1AwCATZjlw2P3LCVUT
T0svbZMFejLA1Ye6Cyf92EaCTaHxa4wI0Ws8ccKlL5UrE/tcxiqsmWFgjoCsXbac
aDvsoRIMGPVqXov7p1AAsEOriLkrAgMBAAECgYBESgzl5oD2XxWF5I2sINdmRpn/
cKPHV+QwpgdTkIlfIjdLfUp1x/S+51O2baucmJKpEu+ZPQTPRz4ne4KBpFpHuf7d
dqzq1oVWJuir0EZDyE+tW970h7D1LS34ZvpoucY3q6MM6i6SWFYOlHyVatho0Yis
8af0vB9TZkbJBFx7wQJBANrXgjhOkARFnfXrtKBl7m48SqAFp3bQ5S1kvnV8BxY2
4KMt+E0n/4Bl1vpxfv2/K5uK7vszGP+RRpxir4l5ODECQQDS83ukalE68A0SRZ93
zZ/DbrztTOfTrs1atmtMfrn+Ut5+ParqJTRgd8q6iwIXZuLEoMXmTL4EEcAjW3hV
FYwbAkEAolbMGsjsvL6H9y3qyDHVNaE0GpEXMWS1HX9mafCfsTL0YRhw4YuSVcU2
PIKFGeb+ilv9/ApjuPIj1069uNVbgQJAaaxrHbvcoUerZKHX9q3dAIuyvLUv1MzW
NkD3k0RQa+SbbGX7/ntQt5qKxdPo4kw6AQIA4RNEhMlXlN63cvfuTQJAJwg+4sRS
0UQvAka4FJ5s/aQOnsYq8a9HtIh2eaefaHF9SbKj2hbjZGWzCueO7pR5zDz1FyGW
GgkX7mQDhcrG7g==
-----END PRIVATE KEY-----";

    private $merchant_public_key = "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC0VO0U5GfchD0Iv3wjjRXBeEwl
fdtRwQgnjOIfkBH8coXIonZTP0HxZQMCbZE9QMAgE2Y5cNj9ywlVE09LL22TBXoy
wNWHugsn/dhGgk2h8WuMCNFrPHHCpS+VKxP7XMYqrJlhYI6ArF22nGg77KESDBj1
al6L+6dQALBDq4i5KwIDAQAB
-----END PUBLIC KEY-----";

    //智付公钥
    private $dinpay_public_key = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCTzX4PppFg1osoKuXF3QcFkSp7m
CXek5BDTl+PiJttgNbbTf9i9Mf4YERTGmhZAyrL9B4/duc7Nsc6Czc//zmBZbt8Xk
vc5mSiUN25XpSxDG4TjjRF28lRPYZs2C2zmHLiUuWmR2xIPT8aULhcfN+iHkGpZU+
kK9RKhNYxM7xDjwIDAQAB
-----END PUBLIC KEY-----';


    //加密密钥
    private $encryption_key = "";
    private $merchant_code = '2130000234';
    private $interface_version = 'V3.0';

    //提交url
    ## nginx域名跳转
    #private $web_pay_url = "https://pay.dinpay.com/gateway?input_charset=UTF-8";
    private $web_pay_url = "http://pay.sxypl.top/index.php?input_charset=UTF-8";

    public function __construct()
    {
        $this->_init();
    }

    public function pay($payment, $amount, $orderNo, $url_callback, $url_return)
    {
        $params = array();
        $merchant_code = $this->merchant_code;//商户号，1118004517是测试商户号，线上发布时要更换商家自己的商户号！
        $service_type = "direct_pay";
        $interface_version = "V3.0";
        $sign_type = "RSA-S";
        $input_charset = "UTF-8";
        $notify_url = $url_callback;
        $order_no = $orderNo;
        $order_time = date('Y-m-d H:i:s');
        $order_amount = $amount;
        $product_name = "用户充值";
//以下参数为可选参数，如有需要，可参考文档设定参数值
        $return_url = $url_return;
        $return_url = '';
        $pay_type = "";
        $redo_flag = "";
        $product_code = "";
        $product_desc = "";
        $product_num = "";
        $show_url = "";
        $client_ip = "";
        $bank_code = "";
        $extend_param = "";
        $extra_return_param = "";
/////////////////////////////   参数组装  /////////////////////////////////
        /**
         * 除了sign_type参数，其他非空参数都要参与组装，组装顺序是按照a~z的顺序，下划线"_"优先于字母
         */

        $signStr = "";

        if ($bank_code != "") {
            $signStr = $signStr . "bank_code=" . $bank_code . "&";
        }
        if ($client_ip != "") {
            $signStr = $signStr . "client_ip=" . $client_ip . "&";
        }
        if ($extend_param != "") {
            $signStr = $signStr . "extend_param=" . $extend_param . "&";
        }
        if ($extra_return_param != "") {
            $signStr = $signStr . "extra_return_param=" . $extra_return_param . "&";
        }
        $signStr = $signStr . "input_charset=" . $input_charset . "&";
        $signStr = $signStr . "interface_version=" . $interface_version . "&";
        $signStr = $signStr . "merchant_code=" . $merchant_code . "&";
        $signStr = $signStr . "notify_url=" . $notify_url . "&";
        $signStr = $signStr . "order_amount=" . $order_amount . "&";
        $signStr = $signStr . "order_no=" . $order_no . "&";
        $signStr = $signStr . "order_time=" . $order_time . "&";

        if ($pay_type != "") {
            $signStr = $signStr . "pay_type=" . $pay_type . "&";
        }

        if ($product_code != "") {
            $signStr = $signStr . "product_code=" . $product_code . "&";
        }
        if ($product_desc != "") {
            $signStr = $signStr . "product_desc=" . $product_desc . "&";
        }

        $signStr = $signStr . "product_name=" . $product_name . "&";

        if ($product_num != "") {
            $signStr = $signStr . "product_num=" . $product_num . "&";
        }
        if ($redo_flag != "") {
            $signStr = $signStr . "redo_flag=" . $redo_flag . "&";
        }
        if ($return_url != "") {
            $signStr = $signStr . "return_url=" . $return_url . "&";
        }

        $signStr = $signStr . "service_type=" . $service_type;

        if ($show_url != "") {

            $signStr = $signStr . "&show_url=" . $show_url;
        }

/////////////////////////////   获取sign值（RSA-S加密）  /////////////////////////////////

        ##商户私钥
        $merchant_private_key = openssl_get_privatekey($this->merchant_private_key);

        openssl_sign($signStr, $sign_info, $merchant_private_key, OPENSSL_ALGO_MD5);
        $sign = base64_encode($sign_info);

        $html = <<<EOT
<form id="pay_action_form" method="post" action="{$this->web_pay_url}" target="_blank">
    <input type="hidden" name="sign" value="{$sign}"/>
    <input type="hidden" name="merchant_code" value="{$merchant_code}"/>
    <input type="hidden" name="bank_code" value="{$bank_code}"/>
    <input type="hidden" name="order_no" value="{$order_no}"/>
    <input type="hidden" name="order_amount" value="{$order_amount}"/>
    <input type="hidden" name="service_type" value="{$service_type}"/>
    <input type="hidden" name="input_charset" value="{$input_charset}"/>
    <input type="hidden" name="notify_url" value="{$notify_url}">
    <input type="hidden" name="interface_version" value="{$interface_version}"/>
    <input type="hidden" name="sign_type" value="{$sign_type}"/>
    <input type="hidden" name="order_time" value="{$order_time}"/>
    <input type="hidden" name="product_name" value="{$product_name}"/>
    <input Type="hidden" Name="client_ip" value="{$client_ip}"/>
    <input Type="hidden" Name="extend_param" value="{$extend_param}"/>
    <input Type="hidden" Name="extra_return_param" value="{$extra_return_param}"/>
    <input Type="hidden" Name="pay_type" value="{$pay_type}"/>
    <input Type="hidden" Name="product_code" value="{$product_code}"/>
    <input Type="hidden" Name="product_desc" value="{$product_desc}"/>
    <input Type="hidden" Name="product_num" value="{$product_num}"/>
    <input Type="hidden" Name="return_url" value="{$return_url}"/>
    <input Type="hidden" Name="show_url" value="{$show_url}"/>
    <input Type="hidden" Name="redo_flag" value="{$redo_flag}"/>
</form>
EOT;

        $json['pay_type'] = "FORM";
        $json['pay_act'] = "post";
        $json['pay_html'] = $html;

        return $json;
    }

    public function pay_old($payment, $amount, $orderNo, $url_callback, $url_return)
    {

        $order_time = date("Y-m-d H:i:s");
/////////////////////////////////获取商家私钥//////////////////////////////////////
////////////////////////get the private key of merchant///////////////////////////
        $priKey = openssl_get_privatekey($this->merchant_private_key);
/////////////////////////////////初始化提交参数//////////////////////////////////////
////////////////////////initial the parameter datas/////////////////////////////////
        $merchant_code = $this->merchant_code;
        $service_type = 'direct_pay';
        $interface_version = $this->interface_version;
        $pay_type = "";
        $sign_type = "RSA-S";
        $input_charset = "UTF-8";
        $notify_url = $url_callback;

        $product_code = "";
        $product_desc = "";
        $product_num = "";
        $show_url = "";
        $client_ip = "";
        $bank_code = "";
        $redo_flag = "";
        $extend_param = "";
        $extra_return_param = "";
        $return_url = $url_return;

/////////////////////////////   数据签名  /////////////////////////////////
////////////////////////////  Data signature  ////////////////////////////
        /**
         * 签名规则定义如下：
         * （1）参数列表中，除去sign_type、sign两个参数外，其它所有非空的参数都要参与签名，值为空的参数不用参与签名；
         * （2）签名顺序按照参数名a到z的顺序排序，若遇到相同首字母，则看第二个字母，以此类推，同时将商家支付密钥key放在最后参与签名，组成规则如下：
         * 参数名1=参数值1&参数名2=参数值2&……&参数名n=参数值n&key=key值
         */
        $signStr = "";
        if ($bank_code != "") {
            $signStr = "bank_code=" . $bank_code . "&";
        }
        if ($client_ip != "") {
            $signStr = $signStr . "client_ip=" . $client_ip . "&";
        }
        if ($extend_param != "") {
            $signStr = $signStr . "extend_param=" . $extend_param . "&";
        }
        if ($extra_return_param != "") {
            $signStr = $signStr . "extra_return_param=" . $extra_return_param . "&";
        }
        $signStr = $signStr . "input_charset=" . $input_charset . "&";
        $signStr = $signStr . "interface_version=" . $interface_version . "&";
        $signStr = $signStr . "merchant_code=" . $merchant_code . "&";
        $signStr = $signStr . "notify_url=" . $notify_url . "&";
        $signStr = $signStr . "order_amount=" . $amount . "&";
        $signStr = $signStr . "order_no=" . $orderNo . "&";
        $signStr = $signStr . "order_time=" . $order_time . "&";
        if ($pay_type != "") {
            $signStr = $signStr . "pay_type=" . $pay_type . "&";
        }
        if ($product_code != "") {
            $signStr = $signStr . "product_code=" . $product_code . "&";
        }
        if ($product_desc != "") {
            $signStr = $signStr . "product_desc=" . $product_desc . "&";
        }
        $signStr = $signStr . "product_name=" . "用户充值" . "&";
        if ($product_num != "") {
            $signStr = $signStr . "product_num=" . $product_num . "&";
        }
        if ($redo_flag != "") {
            $signStr = $signStr . "redo_flag=" . $redo_flag . "&";
        }
        if ($return_url != "") {
            $signStr = $signStr . "return_url=" . $return_url . "&";
        }
        if ($show_url != "") {
            $signStr = $signStr . "service_type=" . $service_type . "&";
            $signStr = $signStr . "show_url=" . $show_url;
        } else {
            $signStr = $signStr . "service_type=" . $service_type;
        }
        openssl_sign($signStr, $sign_info, $priKey, OPENSSL_ALGO_MD5);
        $sign = base64_encode($sign_info);

        $postData['merchant_code'] = $this->merchant_code;
        $postData['service_type'] = $service_type;
        $postData['interface_version'] = $this->interface_version;
        $postData['input_charset'] = $input_charset;
        $postData['notify_url'] = $url_callback;
        $postData['sign_type'] = $sign_type;
        $postData['sign'] = $sign;
        $postData['return_url'] = $url_return;
        $postData['pay_type'] = $pay_type;
        $postData['client_ip'] = $client_ip;
        $postData['order_no'] = $orderNo;
        $postData['order_time'] = $order_time;
        $postData['order_amount'] = $amount;
        $postData['product_name'] = "用户充值";
        $postData['product_code '] = $product_code;
        $postData['show_url'] = $show_url;
        $postData['product_num'] = $product_num;
        $postData['product_desc'] = $product_desc;
        $postData['bank_code'] = $bank_code;
        $postData['extra_return_param'] = $extra_return_param;
        $postData['extend_param'] = $extend_param;
        $postData['redo_flag'] = $redo_flag;


        $html = <<<EOT
<form id="pay_action_form" method="post" action="{$this->web_pay_url}" target="_blank">
    <input type="hidden" name="sign" value="{$sign}"/>
    <input type="hidden" name="merchant_code" value="{$merchant_code}"/>
    <input type="hidden" name="bank_code" value="{$bank_code}"/>
    <input type="hidden" name="order_no" value="{$orderNo}"/>
    <input type="hidden" name="order_amount" value="{$amount}"/>
    <input type="hidden" name="service_type" value="{$service_type}"/>
    <input type="hidden" name="input_charset" value="{$input_charset}"/>
    <input type="hidden" name="notify_url" value="{$notify_url}">
    <input type="hidden" name="interface_version" value="{$interface_version}"/>
    <input type="hidden" name="sign_type" value="{$sign_type}"/>
    <input type="hidden" name="order_time" value="{$order_time}"/>
    <input type="hidden" name="product_name" value="用户充值"/>
    <input Type="hidden" Name="client_ip" value="{$client_ip}"/>
    <input Type="hidden" Name="extend_param" value="{$extend_param}"/>
    <input Type="hidden" Name="extra_return_param" value="{$extra_return_param}"/>
    <input Type="hidden" Name="pay_type" value="{$pay_type}"/>
    <input Type="hidden" Name="product_code" value="{$product_code}"/>
    <input Type="hidden" Name="product_desc" value="{$product_desc}"/>
    <input Type="hidden" Name="product_num" value="{$product_num}"/>
    <input Type="hidden" Name="return_url" value="{$return_url}"/>
    <input Type="hidden" Name="show_url" value="{$show_url}"/>
    <input Type="hidden" Name="redo_flag" value="{$redo_flag}"/>

</form>
EOT;

        $json['pay_type'] = "FORM";
        $json['pay_act'] = "post";
        $json['pay_html'] = $html;

        return $json;
    }

    public function callback($sucFuc = null)
    {
        $merchant_code = $_POST["merchant_code"];
        $interface_version = $_POST["interface_version"];
        $sign_type = $_POST["sign_type"];
        $dinpaySign = base64_decode($_POST["sign"]);
        $notify_type = $_POST["notify_type"];
        $notify_id = $_POST["notify_id"];
        $order_no = $_POST["order_no"];
        $order_time = $_POST["order_time"];
        $order_amount = $_POST["order_amount"];
        $trade_status = $_POST["trade_status"];
        $trade_time = $_POST["trade_time"];
        $trade_no = $_POST["trade_no"];
        $bank_seq_no = $_POST["bank_seq_no"];
        $extra_return_param = $_POST["extra_return_param"];

/////////////////////////////   参数组装  /////////////////////////////////
        /**
         * 除了sign_type dinpaySign参数，其他非空参数都要参与组装，组装顺序是按照a~z的顺序，下划线"_"优先于字母
         */

        $signStr = "";
        if ($bank_seq_no != "") {
            $signStr = $signStr . "bank_seq_no=" . $bank_seq_no . "&";
        }

        if ($extra_return_param != "") {
            $signStr = $signStr . "extra_return_param=" . $extra_return_param . "&";
        }

        $signStr = $signStr . "interface_version=" . $interface_version . "&";

        $signStr = $signStr . "merchant_code=" . $merchant_code . "&";

        $signStr = $signStr . "notify_id=" . $notify_id . "&";

        $signStr = $signStr . "notify_type=" . $notify_type . "&";

        $signStr = $signStr . "order_amount=" . $order_amount . "&";

        $signStr = $signStr . "order_no=" . $order_no . "&";

        $signStr = $signStr . "order_time=" . $order_time . "&";

        $signStr = $signStr . "trade_no=" . $trade_no . "&";

        $signStr = $signStr . "trade_status=" . $trade_status . "&";

        $signStr = $signStr . "trade_time=" . $trade_time;

        //echo $signStr;

/////////////////////////////   RSA-S验证  /////////////////////////////////


        $dinpay_public_key = openssl_get_publickey($this->dinpay_public_key);

        $flag = openssl_verify($signStr, $dinpaySign, $dinpay_public_key, OPENSSL_ALGO_MD5);

///////////////////////////   响应“SUCCESS” /////////////////////////////
        if ($flag) {
            echo "SUCCESS";
            $params = [];
            $params['orderNo'] = $order_no;
            $params['tradeAmt'] = $order_amount;
            $params['payment_id'] = $this->payment_id;
            call_user_func($sucFuc, $params);
        } else {
            echo "Verification Error";
        }
    }

    private function _init()
    {

    }
}