<?php 
class Turbo_Utils{
	public function  get_time_ago($timestamp, $granularity=2, $format='Y-m-d H:i:s'){
		$int_timestamp = strtotime($timestamp);
		if($int_timestamp == FALSE){
			return "cannot format: {$timestamp}";
		}
		
        $difference = time() - $int_timestamp;
        if($difference < 0) return '0 seconds ago';
        elseif($difference < 864000){
                $periods = array('week' => 604800,'day' => 86400,'hr' => 3600,'min' => 60,'sec' => 1);
                $output = '';
                foreach($periods as $key => $value){
                        if($difference >= $value){
                                $time = round($difference / $value);
                                $difference %= $value;
                                $output .= ($output ? ' ' : '').$time.' ';
                                $output .= (($time > 1 && $key == 'day') ? $key.'s' : $key);
                                $granularity--;
                        }
                        if($granularity == 0) break;
                }
                return ($output ? $output : '0 seconds').' ago';
        }
        else return date($format, $int_timestamp);
	}
	public function curl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_NOBODY, FALSE); // remove body
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}