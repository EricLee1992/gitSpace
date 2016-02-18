<?php 
	$db_server = 'nwt-2.l001.51vhost.net';//'qdm163951542.my3w.com';
	$db_account = 'db03152';//'qdm163951542';
	$db_password = '51Bc1B1B';//'Woshizhu3312';
	$db_name = 'db03152';//'qdm163951542_db';

	$conn = new mysqli($db_server, $db_account, $db_password, $db_name, '3306');
	if ($conn->connect_error) {
		die('数据库连接失败！' . $conn->connection_error);
		return;
	}
	$conn->query('set names utf8');
	//插入信息
	$userKey = getKey();
	function insertUserInfo($conn, $userKey) {
		$sql = 'insert into game_redbag_data(ip, start_time, userKey) values("'.getIP().'","'.date('y-m-d H:i:s').'", "'.$userKey.'")';
		$conn->query($sql);
	}
	insertUserInfo($conn, $userKey);
	//生成随机码
	function getKey() {
		return uniqid('user', true);
	}
	//获取ip
	function getIP() {
		$ip = '未知ip';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
		} else if (!empty($_SERVER['HTTP_X_FORWARD_FOR'])) {
			return is_ip($_SERVER['HTTP_X_FORWARD_FOR'])?$_SERVER['HTTP_X_FORWARD_FOR']:$ip;
		} else {
			return is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
		}	
	}
	//判断字符串是否为ip
	function is_ip($str) {
		$ipArr = explode('.', $str);
		$size = count($ipArr);
		for ($i = 0; $i < $size; $i++) {
			if ($ipArr[$i] > 255) {
				return false;
			}
		}
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
	}
	//随机获取头像
	function getAvatar($conn) {
		$arr = array(1, 3, 5, 7, 9);
		$i = rand(0, 4);
		$sql = 'select name, path from game_redbag_avatar where type = 0 and id='.$arr[$i].' or type = 1 and id='.($arr[$i] + 1).' order by id';
		$result = $conn->query($sql);
		return $result;
	}

	//查询聊天界面文案
	function getLetters($type_code, $conn) {
		$sql = 'select content from game_redbag_letters where type_code = ' . $type_code . ' order by item';
		$result = $conn->query($sql);
		return $result;
	}
	
	//查询初始头像
	$def_ava = getAvatar($conn);
	$avatar_path_rect = '';
	$avatar_path_circle = '';
	$avatar_name;
	if ($def_ava->num_rows == 2) {
		while ($row = $def_ava->fetch_assoc()) {
			if ($avatar_path_rect != '') {
				$avatar_path_circle = $row['path'];
			} else {
				$avatar_path_rect = $row['path'];
			}
			$avatar_name = $row['name'];
		}
	}

	//查询文案
	$def_let_result = getLetters('01', $conn);
	$def_let = array();
	if ($def_let_result->num_rows > 0) {
		while ($row = $def_let_result->fetch_assoc()) {
			array_push($def_let, $row['content']);
		}
	}
	$conn->close;
	$resp = array('avatar_name'=>$avatar_name, 'avatar_path_rect'=>$avatar_path_rect, 'avatar_path_circle'=>$avatar_path_circle, 'def_let'=>$def_let, 'userKey'=>$userKey);
	echo json_encode($resp);
?>