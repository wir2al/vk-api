<?php
// http://oauth.vk.com/oauth/authorize?client_id=3127341&scope=notify,friends,photos,audio,video,docs,notes,pages,status,wall,groups,messages,notifications,stats,ads,offline&display=page&response_type=token

function wallpost($vkid, $message) {
	return file_get_contents('https://api.vkontakte.ru/method/wall.post?owner_id=' . $vkid . '&access_token=164f3db6137121211371212171135e990c11371136e9dbf70ab44ed8d697599&message=' . $message . '&from_group=1&signed=1&attachments=photo-30051872_289192004');
}

function walldelete($vkid, $post_id) {
	return file_get_contents('https://api.vkontakte.ru/method/wall.delete?owner_id=' . $vkid . '&access_token=164f3db6137121211371212171135e990c11371136e9dbf70ab44ed8d697599&post_id=' . $post_id);
}

function num_ending($number, $end1, $end2, $end3) {
	$num100 = $number % 100;
	$num10 = $number % 10;
	if ($num100 >= 5 && $num100 <= 20) {
		return $end1;
	} else if ($num10 == 0) {
		return $end1;
	} else if ($num10 == 1) {
		return $end2;
	} else if ($num10 >= 2 && $num10 <= 4) {
		return $end3;
	} else if ($num10 >= 5 && $num10 <= 9) {
		return $end1;
	} else {
		return $number;
	}
}
	
$timearray = array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять', 'десять');

$timeleft = floor((1357878600 - time()) / 86400);
	
$message = 'До зимней сессии ';
$message .= num_ending($timeleft, 'осталось', 'остался', 'осталось');
$message .= ' ';

if($timeleft > 10) {
	$message .= $timeleft . ' ' . num_ending($timeleft, 'дней', 'день', 'дня') . '.';
} else {
	$message .= $timearray[$timeleft] . ' ' . num_ending($timeleft, 'дней', 'день', 'дня') . '!';
}

$wallpost = wallpost('-30051872', str_replace (' ', '%20', $message));
$response = json_decode($wallpost, true);
$post_id = $response['response']['post_id'];

if(empty($post_id)) {
	echo $response['error']['error_msg'];
	exit();
} else {
	$last_post_id = file_get_contents('./last_post_id.txt');
	if(!empty($last_post_id)) {
		$walldelete = walldelete('-30051872', $last_post_id);
	}
	
	$fp = fopen('./last_post_id.txt', 'w+');
	fwrite($fp , $post_id);
	echo 'Сообщение «' . $message . '» успешно опубликовано под номером ' . $post_id . '.';
}
?>