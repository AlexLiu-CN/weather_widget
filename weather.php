<?php
//获取ip
function get_client_ipv4(){
    $ip_check_url='http://whatismyip.akamai.com/';
    $client_ipv4 = file_get_contents($ip_check_url);
    //echo $client_ipv4;
    return $client_ipv4;
}
get_client_ipv4();
//生成签名后的url
function encode_url()
{
    $public_key = "PQAfIBWzrg7L9gMUw";
    $private_key = "SppbwVqK1iJZ2t9N8";
    $api = 'https://api.seniverse.com/v3/weather/daily.json'; // 接口地址
    $location = get_client_ipv4();
// 生成签名
    $param = [
        'ts' => time(),
        'ttl' => 1800,
        'uid' => $public_key,
    ];
    $sig_data = http_build_query($param); // http_build_query 会自动进行 url 编码
// 使用 HMAC-SHA1 方式，以 API 密钥（key）对上一步生成的参数字符串（raw）进行加密，然后 base64 编码
    $sig = base64_encode(hash_hmac('sha1', $sig_data, $private_key, TRUE));
// 拼接 url 中的 get 参数
    $param['sig'] = $sig; // 签名
    $param['location'] = $location;
    $param['start'] = 0; // 开始日期。0 = 今天天气
    $param['days'] = 3; // 查询天数，1 = 只查一天
// 构造url
    $encoded_weather_url = $api . '?' . http_build_query($param);
    //echo $encoded_weather_url;

    return $encoded_weather_url;
}

function decode_weather_json()
{
    $json_raw = file_get_contents(encode_url());
    var_dump(json_decode($json_raw, true));

}


decode_weather_json();


?>