<?php 
header('Content-type: text/xml');
$host = $_SERVER['HTTP_HOST'];
$out = '<config>
	<autoPlay>false</autoPlay>
	<controlBarAutoHide>true</controlBarAutoHide>
	<controlBarPosition>bottom</controlBarPosition>
	<playButtonOverlay>false</playButtonOverlay>
	<skin>/sites/default/modules/webtv/skin-mavic.xml</skin>
	<videoService>http://'.$host.'/sites/default/modules/webtv/service/service-webtv.php</videoService>
</config>
';
print $out;
?>