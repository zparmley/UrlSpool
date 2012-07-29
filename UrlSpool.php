<?php
$usage = <<<USG
USAGE:
php UrlSpool.php pattern datalists [count]
	pattern : see readme for syntax
	datalists : a valid json encoded array of arrays
	count : optional, defaults to 1, number of line-delimited urls to output
USG;


// Check inputs
if (count($argv) < 3) {
	die($usage);
}

$outcount = 1;
if (count($argv) >= 4) {
	$outcount = (int) $argv[3];
}

$pattern = $argv[1];
$lists = json_decode($argv[2]);

if (is_null($lists) === true) {
	die('datalists must be valid json, did not decode');
}

// var_dump($argv, $pattern, $lists);
// die();

// $lists = array(array('get', 'set', 'flush'), array('mek', 'wek', 'bek'), array(1, 2, 3));
// $pattern = 'http://kvstore.com/$0<$0!=flush~/$1<$0=set~/$2>>';


// 3.6 seconds to build 100000 urls
for ($i = 0; $i < $outcount; $i++) {
	if ($i !== 0) {
		echo "\n";
	}
	echo parseUrl($pattern, $lists);
}


function parseUrl($pattern, $lists) {
	$url = '';
	$nowrite = 0;
	$reg = array();
	for ($pnt = 0; $pnt < strlen($pattern); $pnt++) {
		$char = $pattern[$pnt];
		if ($char === '>') {
			$nowrite = ($nowrite > 1) ? $nowrite - 1 : 0;
			continue;
		}

		if ($char === '<') {
			if ($nowrite > 0) {
				$nowrite += 1;
			} else {
				$pnt += 2;
				$pivot = $reg[(int) $pattern[$pnt]];
				$cond = ($pattern[$pnt + 1] === '!') ? false : true;
				$condVal = '';
				$pnt = ($cond) ? $pnt + 2 : $pnt + 3;
				while ($pattern[$pnt] !== '~') {
					$condVal .= $pattern[$pnt++];
				}
				if (!(($pivot !== $condVal) xor $cond)) {
					$nowrite += 1;
				}
			}
			continue;
		} elseif ($nowrite === 0) {
			if ($char === '$') {
				$nid = (int) $pattern[++$pnt];
				if (!isset($reg[$nid])) {
					$reg[$nid] = $lists[$nid][array_rand($lists[$nid])];
				}

				$url .= $reg[$nid];
			} else {
				$url .= $char;
			}
		}
	}

	return $url;

}