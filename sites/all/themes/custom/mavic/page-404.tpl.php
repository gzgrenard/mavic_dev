<?php require('header.php'); ?>
<script type="text/javascript" >

$(document).ready(function() {	
		$("#body-background").ezBgResize();	
		getSessionProductCompare();
		checkSize();
	});
</script>

		<div id="body-background"><img src="<?php echo $landscape?>?v=1" width="1354" height="900" alt="Bg"></div>
		<div id="container">
			<div id="black_screen"></div>
			<div id="subcontainer">
				<?php include('menu.php') ?>
				<div id="main_content">
					<div id="breadcrumb">
						<?php echo l(t('Home'),'<front>'); ?>
					</div>
					<?php 
						echo $toto1.$toto2;
						switch($lang) { 
							case 'fr' : 
						?>
							<h1 class="helvetica" style="margin-top:35px;font-size:24px;">nouvelle gamme 2011<br/>nouveau site internet</h1>
							<p style="width:600px;">A l’occasion de la mise en ligne de la nouvelle offre, le site <a style="font-weight:bold;" href="http://www.mavic.com/fr">www.mavic.com</a> a été totalement revu : 
								<br/><br/>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">fiches produits plus complètes</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">les vidéos Mavic visibles en plein écran et en haute définition</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">outils d’aide au choix disponibles sur les pages de gamme (tri des produits et comparateur)</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">rubrique Mavic complète pour tout savoir sur la marque Mavic (détaillants, news, assistance, athlètes, présentation de la société)</span>
								<br/>
								De nouvelles fonctionnalités seront progressivement ajoutées au cours des prochains mois. 
								<br/><br/>
								L’équipe Mavic vous souhaite une agréable navigation sur le site <a style="font-weight:bold;" href="http://www.mavic.com/fr">www.mavic.com</a>
								<a href="http://www.mavic.com/fr" class="button_404">découvrir la nouvelle gamme Mavic</a>
							</p>
						<?php
							break;
							case 'de' : 
						?>
							<h1 class="helvetica" style="margin-top:35px;font-size:24px;">Modellneuheiten 2011<br/>Neue Homepage</h1>
							<p style="width:600px;">Zusammen mit der Präsentation des neuen Modellprogramms wurde auch die Homepage <a style="font-weight:bold;" href="http://www.mavic.com/de">www.mavic.com</a> komplett überarbeitet:<br/><br/>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Umfassende Produkt-Informationen</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Mavic-Videos – auch im Vollbild-Modus in höchster Auflösung</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Auswahlhilfe-Tools auf den Produkt-Seiten (Produkt-Filter, Produkt-Vergleich)</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Umfassende Informationen über Mavic (Händler, News, Rennservice, Athleten, Firmen-Präsentation)</span>
								<br/>
								In den nächsten Monaten werden noch weitere neue Features hinzugefügt.<br/><br/>
								Das gesamte Mavic-Team wünscht Ihnen viel Spass beim Stöbern auf unserer Homepage <a style="font-weight:bold;" href="http://www.mavic.com/de">www.mavic.com</a>
								<a href="http://www.mavic.com/de" class="button_404">Zum neuen Mavic-Modellprogramm</a>
							</p>
						<?php
							break;
							case 'it' : 
						?>
							<h1 class="helvetica" style="margin-top:35px;font-size:24px;">nuova gamma 2011<br/> nuovo sito</h1>
							<p style="width:600px;">Seguendo la presentazione della nuova offerta in linea, il sito  <a style="font-weight:bold;" href="http://www.mavic.com/it">www.mavic.com</a> è stato completamente rinnovato: <br/><br/>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">schede prodotto esaurienti</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Video Mavic visualizzabili a tutto schermo in alta qualità</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">scelta di strumenti di assistenza disponibili nella pagine della gamma (filtri prodotto, comparatori)</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">informazioni complete su Mavic per sapere tutto sul marchio (rivenditori, notizie, assistenza, atleti, presentazione azienda)</span>
								<br/>
								Nuove caratteristiche saranno aggiunte progressivamente nei prossimi mesi. <br/><br/>
								Il Team Mavic ti augura buona navigazione sul sito <a style="font-weight:bold;" href="http://www.mavic.com/it">www.mavic.com</a><br/>
								<a class="button_404" href="http://www.mavic.com/it">vedi la nuova gamma Mavic</a>
							</p>
						<?php
							break;
							case 'es' : 
						?>
							<h1 class="helvetica" style="margin-top:35px;font-size:24px;">Nueva gama 2011<br/>  Nuevo sitio web</h1>
							<p style="width:600px;">A la vez que presentamos la nueva gama, hemos renovado totalmente nuestra web <a style="font-weight:bold;" href="http://www.mavic.com/es">www.mavic.com</a>  : 
								<br/><br/>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Fichas de producto más completas </span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Videos Mavic de alta resolución para ver a pantalla completa</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Herramienta de selección disponible en la página de cada gama (filtrar productos, comparar)</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Sección completa Mavic para conocer todo sobre la marca Mavic (tiendas, noticias, servicio de asistencia, atletas, presentación de la empresa)</span>
								<br/>
								Durante los próximos meses iremos añadiendo nuevas funciones. <br/><br/>
								Todo el equipo Mavic le desea una agradable visita a nuestra web  <a style="font-weight:bold;" href="http://www.mavic.com/es">www.mavic.com</a>
								<a href="http://www.mavic.com/es" class="button_404">Descubra la nueva gama Mavic</a>
							</p>
						<?php
							break;
							case 'ja' : 
						?>
							<h1 class="helvetica" style="margin-top:35px;font-size:24px;">NEW 2011 レンジ<br/>  NEW ウェブサイト</h1>
							<p style="width:600px;"><a style="font-weight:bold;" href="http://www.mavic.com/ja">www.mavic.com</a> がリニューアルいたしました。 : 
								<br/><br/>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">フルラインアップのプロダクトシート</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">フルスクリーン＆高解像度でムービーを見る</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">商品レンジページ内での検索機能（フィルター、比較ツール）</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">MAVICに関する見出し一覧（ショップ検索、ニュートラルサービス、選手、会社紹介）</span>
								<br/>
								新しい記事が追加されます。<br/><br/>
								<a style="font-weight:bold;" href="http://www.mavic.com/ja">www.mavic.com</a> を閲覧いただきありがとうございます。<br />
								<a href="http://www.mavic.com/ja" class="button_404">MAVICの新しいレンジを見る</a>
							</p>
						<?php 
							break;
							default : 
						?>
							<h1 class="helvetica" style="margin-top:35px;font-size:24px;">new 2011 range<br/> new website</h1>
							<p style="width:600px;">
								Following the new offer presentation online, the  <a style="font-weight:bold;" href="http://www.mavic.com">www.mavic.com</a> website has been fully renewed:<br/><br/>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">exhaustive product sheets</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">Mavic videos viewable on full screen and high quality</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">choice assistance tools available on range pages (product filters and comparisons)</span>
								<span style="display:block;padding-left:13px;margin-left:20px;background:url('http://ns22495.ovh.net/sites/default/themes/mavic/images/puce.gif') no-repeat;background-position: 0px 7px;">full Mavic heading to know everything about the brand (dealers, news, assistance, athletes, company presentation)</span>
								<br/>
								New features will be progressively added in the coming months.<br/><br/>
								The Mavic team hopes you enjoy browsing on the <a style="font-weight:bold;" href="http://www.mavic.com">www.mavic.com</a> website.
								<a href="http://www.mavic.com" class="button_404">check out the new Mavic range</a>
							</p>
						<?php break; ?>
					<?php } ?>
				</div>
			</div>
			<div id="forScrollTop" class="clear"></div>
		<div id="logo_container">
			<?php echo l($breadcrumb[0]['link']['title'],$breadcrumb[0]['link']['href'], array('attributes' => array('id' => 'logo', 'title' => $breadcrumb[0]['link']['title']))); ?>
		</div>
	</div><!-- container -->

	<div id="footer">
		<?php print $footer;?>
	</div>

<!-- omniture -->
<script language="JavaScript" type="text/javascript"><!--
		s.pageType="errorPage";
</script>
<?php require("footer.php");