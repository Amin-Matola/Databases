<?php

class Mail_Master{

	public $server;

	public $port;

	public $socket;

	public $con;

	public $user;

	public $pass;

	public function __construct( $server = '', $port ='', $auth = [] ){
		$this->server 					= $server;

		$this->port   					= $port;

		$this->con    					= $this->server.":".$this->port;

		$this->set_auth($auth);

		$this->socket = @stream_socket_client($this->con, $e, $e_str, 100);


		stream_context_set_option($this->socket, 'ssl', 'verify_peer', false);
        stream_context_set_option($this->socket, 'ssl', 'verify_peer_name', false);
        stream_context_set_option($this->socket, 'ssl', 'allow_self_signed', true);

        $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;

        $this->process();

		
	}

	public function set_auth($auth = []){
		list($this->user, 
			 $this->pass) 				= $this->encode_array($auth);
	}

	public function encode_array($array = []){
		return array_map(function($x){ return base64_encode($x);}, $array);
	}

	public function process(){
		if(!is_resource($this->socket)){
			print_r("Connection failed");
		}

		//return $this->socket;

		$this->login();

		//$resp = stream_get_meta_data($data);
	}

	public function send($data){
		$res = fwrite($this->socket, $data."\n");
		return $res;
	}

	public function receive($bytes = 1024){
		return fgets($this->socket, $bytes);
	}

	public function login($auth = []){
		if(!empty($auth)){
			$this->set_auth($auth); 
		}

		$this->send("EHLO ".$this->server);
		$this->send("STARTTLS");
		
		$this->send("AUTH LOGIN");

		$this->send($this->user);
		$this->send($this->pass);

		$this->send('VRFY ' . $this->user);

		$this->send("MAIL FROM:<aminmartola@gmail.com>");
		$this->send("RCPT TO:<aminmartola@gmail.com>");

		print_r($this->get_data());

		//stream_context_create()
		//stream_context_set_option(); 
	}

	public function get_data(){
		$res = explode(" ", $this->receive());
		return $res;

	}

	public function send_email(){

	}




}
