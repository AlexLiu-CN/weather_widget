<?php
header("Content-Type: text/html;charset=utf-8");


class getUserIP
{

    public function get_client_ipv4()
    {//获取ip
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
            $ip = getenv("REMOTE_ADDR");
        else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];//一般情况使用
        else
            $ip = file_get_contents('http://whatismyip.akamai.com/');//调试机的IP
        //echo $ip;
        return ($ip);
    }

}

class seniverse_weather_info
{
    var $public_key = "PQAfIBWzrg7L9gMUw";
    var $private_key = "SppbwVqK1iJZ2t9N8";
    var $api = 'https://api.seniverse.com/v3/weather/daily.json'; // 接口地址
    var $location;

    function __construct($location, $days)
    {
        $this->location = $location;
        $this->encode_url($days);
    }

    public function encode_url($days)//生成签名后的url
    {
        $param = [// 生成签名
            'ts' => time(),
            'ttl' => 1800,
            'uid' => $this->public_key,
        ];
        $sig_data = http_build_query($param); // http_build_query 会自动进行 url 编码
// 使用 HMAC-SHA1 方式，以 API 密钥（key）对上一步生成的参数字符串（raw）进行加密，然后 base64 编码
        $sig = base64_encode(hash_hmac('sha1', $sig_data, $this->private_key, TRUE));
// 拼接 url 中的 get 参数
        $param['sig'] = $sig; // 签名
        $param['location'] = $this->location;
        $param['start'] = 0; // 开始日期。0 = 今天天气
        $param['days'] = $days; // 查询天数，1 = 只查一天
        $encoded_weather_url = $this->api . '?' . http_build_query($param);// 构造url
        echo $encoded_weather_url;
        return $encoded_weather_url;
    }

}

$location = new getUserIP();

$test2 = new seniverse_weather_info($location->get_client_ipv4(), 3);


?>

