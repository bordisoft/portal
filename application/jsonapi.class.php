<?php defined('_PORTAL') or die();

Class JsonApi
{
	private $_api_url;
	private $_xid;
	private $_realm;
	private $_server_url;
	private $_api_path;
	private static $instance;

	private function __construct($options)
	{
		$this->_server_url = $options['server_url'];
		$this->_api_path = $options['api_path'];
		$this->_api_url = $options['server_url'].'/'.$options['api_path'].'/';
		$this->_xid = isset($options['xid']) ? $options['xid'] : '';
		$this->_realm = isset($options['realm']) ? $options['realm'] : '';
	}

	public static function getInstance($options)
	{
		if ( is_null( self::$instance ) )
		{
			self::$instance = new self($options);
		}
		return self::$instance;
	}

	public function Call($requests)
	{
		$requests = array('json' => json_encode($requests, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE));
		if(function_exists('curl_version'))
		{
			$fn = '_CurlRequest';
		}
		else if(function_exists('fsockopen'))
		{
			$fn = '_SocketRequest';
		}
// 		else
// 		{
// 			$fn = '_StreamRequest';
// 		}

		$i = $response = 0;
		while(!$response && $i++ < 3)
		{
			$response = json_decode(call_user_func_array(array($this,$fn), array($requests)));
		}

		return $response;
	}

	private function _CurlRequest($requests,$method='POST')
	{
		$ch = curl_init($this->_api_url);
		$options = array(
				CURLOPT_SSL_VERIFYPEER => 0,
				CURLOPT_SSLVERSION => 1,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_TIMEOUT => 60,
				CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.7",
				CURLOPT_COOKIESESSION => 1,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_FORBID_REUSE => 1,
				CURLOPT_POST => (($method=='POST') ? TRUE : FALSE),
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_HEADER	=>	0,
				CURLOPT_COOKIE => 'XID='.$this->_xid.';REALM='.$this->_realm,
				CURLOPT_POSTFIELDS => http_build_query($requests)
		);
		curl_setopt_array($ch,$options);
		return curl_exec($ch);
	}

	private function _SocketRequest($requests,$method='POST')
	{
		$timeout = 1;
		$header = $body = '';
		$method = strtoupper($method);
		$data_str = $method=='GET' ? '?' : '';

		$data_str .= http_build_query($requests);
		$cookie_str = 'XID='.$this->_xid.';REALM='.$this->_realm;
		$crlf = "\r\n";
		$req = $method .' /'. $this->_api_path.'/' .($method == 'GET' ? $data_str : '').' HTTP/1.1' . $crlf;
		$req .= 'Host: '. str_replace('https://','',$this->_server_url) . $crlf;
		$req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12' . $crlf;
		$req .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . $crlf;
		$req .= 'Accept-Language: en-us,en;q=0.5' . $crlf;
		$req .= 'Accept-Encoding: deflate' . $crlf;
		$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . $crlf;
		if (!empty($cookie_str))
		{
			$req .= 'Cookie: '. $cookie_str . $crlf;
		}
		if ($method == 'POST')
		{
			$req .= 'Content-Type: application/x-www-form-urlencoded' . $crlf;
			$req .= 'Content-Length: '. strlen($data_str) . $crlf . $crlf;
			$req .= $data_str;
		}
		else $req .= $crlf;
		if (!($fp = @fsockopen(str_replace('https','tls',$this->_server_url), 443, $errno, $errstr)))
		{
			return json_encode(array('error'=>1,'code'=>$errno,'response'=>$errstr));
		}
		stream_set_timeout($fp, 0, $timeout * 1000);
		fputs($fp, $req);

		do
		{
			$header .= fgets ($fp,128);

		} while (strpos($header, "\r\n\r\n" ) === false);

		while (!feof($fp))
		{
			$body .= fgets ($fp,128);
		}

		fclose($fp);

		$body_length = strlen($body);
		$offset = strpos($body,'{');
		$length = $body_length - $offset - ($body_length - (strrpos($body,'}')+1));

		return substr($body,$offset,$length);
	}

	private function _StreamRequest($requests,$method='POST')
	{
		$options = array(
			'http' => array(
				'method'		=>	$method,
				'header'		=>	"Accept-language: en\r\n".
			      					"Content-type: application/x-www-form-urlencoded\r\n".
									'Cookie: XID='.$this->_xid.';REALM='.$this->_realm,
				'user_agent'	=>  'Mozilla/5.0 Firefox/3.6.12',
				'content'		=>	http_build_query($requests)
			)
		);

		$context  = stream_context_create($options);
		$result = file_get_contents($this->_api_url, false, $context);

		return $result;
	}
}
?>