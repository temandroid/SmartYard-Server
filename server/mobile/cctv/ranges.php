<?php
/**
 * @api {post} /cctv/ranges получить список доступных периодов в архиве
 * @apiVersion 1.0.0
 * @apiDescription ***почти готов***
 *
 * @apiGroup CCTV
 *
 * @apiParam {Number} [cameraId] идентификатор камеры
 *
 * @apiHeader {String} authorization токен авторизации
 *
 * @apiSuccess {String} stream название потока
 * @apiSuccess {Object[]} ranges массив интервалов
 * @apiSuccess {Number} ranges.from метка начала
 * @apiSuccess {Number} ranges.duration продолжительность периода
 */

function getRangesForNimble($host, $port, $stream, $token) {

    $salt= rand(0, 1000000);
    $str2hash = $salt . "/". $token;
    $md5raw = md5($str2hash, true);
    $base64hash = base64_encode($md5raw);
    $request_url = "http://$host:$port/manage/dvr_status/$stream?timeline=true&salt=$salt&hash=$base64hash";

    $data = json_decode(file_get_contents($request_url), true);

    $result = [
        [
        "stream" => $stream,
        "ranges" => []
        ]
    ];

    foreach( $data[0]["timeline"] as $range) {
        $result[0]["ranges"][] = ["from" => $range["start"], "duration" => $range["duration"]];
    }

    return $result;
} 

auth();

$camera_id = (int)@$postdata['cameraId'];

$cameras = loadBackend("cameras");

$cam = $cameras->getCamera($camera_id);
if (!$cam) {
    response(404);
}

$configs = loadBackend("configs");
$nimble_servers = $configs->getNimbleServers();

$host = parse_url($cam['flussonic'], PHP_URL_HOST);
$port = parse_url($cam['flussonic'], PHP_URL_PORT);
$path = parse_url($cam['flussonic'], PHP_URL_PATH);

if ( $path[0] == '/' ) $path = substr($path,1);

$stream = $path;
$management_token = false;

foreach ($nimble_servers as $nimble) {
    if ( $nimble['host'] == $host.":".$port ) {
        $management_token = $nimble['management_token'];
        $management_ip = $nimble['management_ip'];
        $management_port = $nimble['management_port'];
        break;
    }
}

if ($management_token) {
    // Nimble Server
    $ranges = getRangesForNimble( $management_ip, $management_port, $stream, $management_token );
} else {
    // Flussonic Server
    $flussonic_token = $cam['credentials'];
    $request_url = "https://$host:$port/$stream/recording_status.json?from=1525186456&token=$flussonic_token";
    // response(200, $request_url);
    $ranges = json_decode(file_get_contents($request_url), true);
}

response(200, $ranges);
?>