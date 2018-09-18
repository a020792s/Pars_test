<?php

namespace CARiD\Parser;

class curl
{

    private $ch;
    private $host;

    private static
        $instance = null;

    public static function getInstance($host)
    {
        if (null === self::$instance) {
            self::$instance = new self($host);
        }
        return self::$instance;
    }

    private function __construct($host)
    {
        if ($this->ch === false) {
            throw new Exception('CURL failed to initialize');
        }
        $this->ch = curl_init();
        $this->host = $host;
        $this->set(CURLOPT_RETURNTRANSFER, true);
    }

    public function set($name, $value)
    {
        $this->options[$name] = $value;
        curl_setopt($this->ch, $name, $value);
        return $this;
    }

    public function tor()
    {
        $this->set(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        $this->set(CURLOPT_PROXY, 'localhost:9050');
        return $this;
    }

    public function cookie()
    {
        $this->set(CURLOPT_COOKIE, 'store_language=US');
        return $this;
    }

    public function ssl($act)
    {
        $this->set(CURLOPT_SSL_VERIFYPEER, $act);
        $this->set(CURLOPT_SSL_VERIFYHOST, $act);
        return $this;
    }

    public function headers($act)
    {
        $this->set(CURLOPT_HEADER, $act);
        return $this;
    }

    public function follow($param)
    {
        $this->set(CURLOPT_FOLLOWLOCATION, $param);
        return $this;
    }

    public function referer($url)
    {
        $this->set(CURLOPT_REFERER, $url);
        return $this;
    }

    public function agent($agent)
    {
        $this->set(CURLOPT_USERAGENT, $agent);
        return $this;
    }

    public function post($data)
    {
        if ($data === false) {
            $this->set(CURLOPT_POST, false);
            return $this;
        }
        $this->set(CURLOPT_POST, true);
        $this->set(CURLOPT_POSTFIELDS, $data);
        return $this;
    }

    public function add_headers()
    {
        $headers = array(
            'accept : text/html, */*; q=0.01',
            'content-type: application/x-www-form-urlencoded; charset=UTF-8',
            'x-requested-with: XMLHttpRequest'
        );
        foreach ($headers as $h)
            $this->options[CURLOPT_HTTPHEADER][] = $h;
        $this->set(CURLOPT_HTTPHEADER, $this->options[CURLOPT_HTTPHEADER]);
        return $this;
    }

    public function request($url)
    {
        $this->set(CURLOPT_URL, $this->make_url($url));
        $data = curl_exec($this->ch);
        if ($data === false) {
            throw new Exception(curl_error($this->ch), curl_errno($this->ch));
        }
        return $this->process_result($data);
    }

    private function make_url($url)
    {
        if ($url[0] != '/')
            $url = '/' . $url;
        return $this->host . $url;
    }

    function tor_new_identity($tor_ip = '127.0.0.1', $control_port = '9051', $auth_code = '')
    {
        $fp = fsockopen($tor_ip, $control_port, $errno, $errstr, 30);
        if (!$fp) return false;

        //авторизация
        fputs($fp, "AUTHENTICATE $auth_code\r\n");
        $response = fread($fp, 1024);
        list($code, $text) = explode(' ', $response, 2);   // echo $response;
        if ($code != '250') return false;

        //отправляю сигнал для смены промежуточных звеньев
        fputs($fp, "signal NEWNYM\r\n");
        $response = fread($fp, 1024);
        list($code, $text) = explode(' ', $response, 2);
        if ($code != '250') return false;

        fclose($fp);
        return true;
    }

    private function process_result($data)
    {
        $info = curl_getinfo($this->ch);
        $body_part = substr($data, $info['header_size']);
        return $body_part;
    }

}