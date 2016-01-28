<?php
	//数据库信息
	$db_server = "qdm163951542.my3w.com";//qdm163951542.my3w.com
	$db_username = "qdm163951542";//qdm163951542
	$db_password = "Woshizhu3312";//Woshizhu3312
	$db_name = "qdm163951542_db";//qdm163951542_db

	$conn = new mysqli($db_server, $db_username, $db_password, $db_name);
	if ($conn->connect_error) {
		die("Connection failed" . $conn->connection_error);
	}
	
	//获取客户端ip
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
		$ip = explode('.', $str);
		$str_size = count($ip);
		for ($i = 0; $i < $str_size; $i++) {
			if ($ip[$i] > 255) {
				return false;
			}
		}
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $str);
	}

	//客户端数据
	$rec_goal = $_POST['rec_goal'];//获取接收到的分数
	$rec_ipAdd = getIP();//接收到的ip
	$rec_time = date('y-m-d');//接收到的时间
	//只有当分数数据不为空的时候才添加
	if (!empty($rec_goal) && $rec_goal < 100) {
		//插入数据库
		$sql = "insert into game_move_goals(ipAdd, goal, time) values(?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('sss', $rec_ipAdd, $rec_goal, $rec_time);
		$stmt->execute();
	}

	//查询最新数据
	$sql = "select * from game_move_goals order by goal desc limit 20";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		$i = 1;//名次
		while ($row = mysqli_fetch_assoc($result)) {
			if ($i == 1) {
				echo '<div><span>&nbsp&nbsp&nbsp' . $i . '<img src="img/crown.png" width="12" height="12"/></span><span>' . isEmpty($row['goal'], false) . '</span><span>' . isEmpty($row['ipAdd'], false) . '</span><span>' . isEmpty($row['time'], true) . '</span></div>';
			} else {
				echo '<div><span>&nbsp&nbsp&nbsp' . $i . '</span><span>' . isEmpty($row['goal'], false) . '</span><span>' . isEmpty($row['ipAdd'], false) . '</span><span>' . isEmpty($row['time'], true) . '</span></div>';
			}
			$i++;
		}
	}
	//字符串处理，为空显示无数据
	function isEmpty($str, $isTime) {
		if (empty($str)) {
			return '无数据';
		}
		if ($isTime) {
			$str = substr($str, 2);
		}
		if (strlen($str) > 8) {
			return substr($str, 0, 8) . '...';
		}
		return $str;
	} 
	$conn->close();
?>