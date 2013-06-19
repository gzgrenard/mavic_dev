<?php
	header ("Content-type: text/xml;charset=UTF-8");
	
	define("DEFAULT_LANGUAGE", "en");
	define("DATASOURCE", "data-webtv.xml");
	
	iconv_set_encoding("internal_encoding", "ISO-8859-1");
	iconv_set_encoding("output_encoding", "UTF-8");
	
	if( !empty( $_POST["id"] ) && $_POST["id"] != "" )
	{
		$requestedId = $_POST["id"];
		
		$dom = new DomDocument;
		$dom->load( DATASOURCE );
		$listeMedias = $dom->getElementsByTagName('media');
		
		foreach( $listeMedias as $media )
		{
			if( ( $media->hasAttribute("id") ) && ( $media->getAttribute("id") == $requestedId ) )
			{
				$listeVideos = $media->getElementsByTagName('video');
				
				if( !empty( $_POST["language"] ) && ( $_POST["language"] != "" ) )
				{
					$requestedLanguage = $_POST["language"];
				}
				else
				{
					$requestedLanguage = DEFAULT_LANGUAGE;
				}
				
				foreach( $listeVideos as $video )
				{
					
					if( ( $video->hasAttribute("language") ) && ( $video->getAttribute("language") == $requestedLanguage ) )
					{
						echo $dom->saveXML($video);
					}
				}
				
			}
		}

	}
?>