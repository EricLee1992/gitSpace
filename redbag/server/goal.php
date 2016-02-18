<?php 
	$db_server = 'nwt-2.l001.51vhost.net';//'qdm163951542.my3w.com';
	$db_account = 'db03152';//'qdm163951542';
	$db_password = '51Bc1B1B';//'Woshizhu3312';
	$db_name = 'db03152';//'qdm163951542_db';

	$conn = new mysqli($db_server, $db_account, $db_password, $db_name, '3306');
	if ($conn->connect_error) {
		die('连接失败！'.$conn->connection_error);
		return;
	}
	$conn->query('set names utf8');
	//接收成绩
	$rec =  $_POST['goal'];

	//修改用户成绩数据
	function updateUserInfo($conn, $goal, $userKey) {
		$sql = 'update game_redbag_data set goal = "'.$goal.'" , end_time = "'.date("y-m-d H:i:s").'", userKey = "" where userKey = "'.$userKey.'"';
		$conn->query($sql);
	}
	updateUserInfo($conn, $rec, $_POST['userKey']);
	
	//查询成绩阶段
	function queryGoal($conn, $goal) {
		$sql = 'select zanValue, comment, share_comm, percent_min, percent_max, time_min, time_max from game_redbag_comment where time_min <= ? and time_max > ?';
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ss', $goal, $goal);
		$stmt->execute();
		
		$stmt->store_result();
		$stmt->bind_result($zanValue, $comment, $share_comm, $percent_min, $percent_max, $time_min, $time_max);
		$result = array();
		while ($stmt->fetch()) {
			array_push($result, $zanValue, $comment, $share_comm, $percent_min, $percent_max, $time_min, $time_max);
		}
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		return $result;
	}
	//分割结语
	function splitComment($str) {
		$str = $str;
		if (empty($str)) {
			return '呃，服务器睡了。';
		}
		$i = strpos($str, '.');
		if ($i > 0) {
			$str_before = substr($str, 0, $i).'，';
			$str_after = substr($str, $i + 1).'。';
			return array($str_before, $str_after);
		}
		return array(1,2);
	}
	//命中率算法
	function percent($time_min, $time_max, $pec_min, $pec_max, $val) {
		//大于5秒，命中率一律为0
		if ($val > 5) {
			return 0;
		}
		if ($val > 1.51) {
			$time_max = 5;
		}
		$rand_pec = $pec_max - $pec_min;//区间百分比跨度
		$rand_time = $time_max - $time_min;//区间时间跨度
		//当前分数所占区间时间跨度百分比
		$per = ($val - $time_min) / $rand_time;
		//按照这个比分比算出在百分比区间内的值
		$result = $pec_max - ($rand_pec * $per);
		return $result;
	}

	$resp = queryGoal($conn, $rec);
	$comments = splitComment($resp[1]);
	//计算命中率
	$hit_pec = percent($resp[5], $resp[6], $resp[3], $resp[4], $rec);
	$resp = array('zanValue'=>$resp[0], 'comment1'=>$comments[0], 'comment2'=>$comments[1], 'share_comm'=>$resp[2], 'percent'=>$hit_pec);
	echo json_encode($resp);
?>