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

class get_seniverse_weather_info
{
    protected $public_key = "PQAfIBWzrg7L9gMUw";
    protected $private_key = "SppbwVqK1iJZ2t9N8";
    var $api = 'https://api.seniverse.com/v3/weather/daily.json'; // 接口地址

    public function encode_url($location, $HowManyDays)//生成签名后的url(参数：地址/申请几天的天气数据123)
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
        $param['location'] = $location;
        $param['start'] = 0; // 开始日期。0 = 今天天气
        $param['days'] = $HowManyDays; // 查询天数
        $encoded_weather_url = $this->api . '?' . http_build_query($param);// 构造url
        return $encoded_weather_url;

    }
}

class decode_seniverse_weather_info
{
    public $encoded_weather_url;
    public $decoded_json;//存放解密后的json

    function __construct($encoded_weather_url)
    {//构造函数：给入api_url数据
        $this->encoded_weather_url = $encoded_weather_url;
        $this->decoded_json = $this->decode_weather_json();
    }

    function print_day($print_howmany_day)//输出几天的数据,输出多天数据时用
    {
        for ($x = 0; $x <= $print_howmany_day; $x++) {
            $this->print_weather($x);
        }
    }

    function decode_weather_json()//解析json
    {
        $json_raw = file_get_contents($this->encoded_weather_url);
        $weather_result = json_decode($json_raw, true, 6);//转为数组，6深度
        return $weather_result;
    }

    function print_location()
    {//输出城市
        //$all_info = $this->decode_weather_json();
        $city_name = $this->decoded_json['results']['0']['location']['name'];
        //echo "document.write(\"";
        echo $city_name;
        //  echo "\");";

        // return $city_name;
    }

    function print_weather($which_day)//打印天气值，0,1,2
    {
        $weather_result = $this->decoded_json;
        switch ($which_day) {//天数选择
            case 0:
                echo "document.write(\"";
                echo "今天:";
                break;
            case 1:
                echo "document.write(\"";
                echo "明天:";
                break;
            case 2:
                echo "document.write(\"";
                echo "后天:";
                break;
        }

        $weather_day = $weather_result['results']['0']["daily"][$which_day]['text_day'];
        $weather_night = $weather_result['results']['0']["daily"][$which_day]['text_night'];
        $weather_temp_low = $weather_result['results']['0']["daily"][$which_day]['low'];
        $weather_temp_high = $weather_result['results']['0']["daily"][$which_day]['high'];
        $weather_wind_direction = $weather_result['results']['0']["daily"][$which_day]['wind_direction'];
        $weather_wind_scale = $weather_result['results']['0']["daily"][$which_day]['wind_scale'];
        echo $weather_day . "/" . $weather_night . " " . $weather_temp_low . "~" . $weather_temp_high . "℃ " . $weather_wind_direction . "风" . $weather_wind_scale . "级";
        echo "\");";
    }

}

?>
<?php
$city = new getUserIP();
$weathere_data = new get_seniverse_weather_info();
$weathere_json = $weathere_data->encode_url($city->get_client_ipv4(), 3);
$output = new decode_seniverse_weather_info($weathere_json);
echo "document.write(\"" . "<td rowspan='2'>";
echo $output->print_location() . "</td>\");";
echo "document.write(\"" . "<td rowspan='2'>" . "<cite>\");";
echo $output->print_weather(0);
echo "document.write(\"" . "<br>" . "\");";
$output->print_weather(1);
echo "document.write(\"" . "<br>" . "\");";
$output->print_weather(2);
echo "document.write(\"" . "</cite></td>\");";














