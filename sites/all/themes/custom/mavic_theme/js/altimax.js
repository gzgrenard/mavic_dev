
jQuery(document).ready(function(){
	
	
	resizeSlider();
	jQuery(window).resize(function(){
		resizeSlider();
	});
	var anchor = getHash();
	if(anchor != ""){
		jQuery('.view-id-fiche_d_tail_choix_d_clinaison.view-display-id-block_2 li').each(function(){
			if(jQuery(this).text() == anchor){
				changeDeclinaison(jQuery(this).index());
			}
			
		});
	}
	jQuery(".view-fiche-produit-galerie-couleur  .views-field-field-d-clinaisons .field-type-text").click(function(){
		var listItem = jQuery(this).parent().find(".field-type-text");
		var index = jQuery(this).index(listItem);
		jQuery(this).parent().find(".field-type-image").hide();
		jQuery(this).parent().find(".field-type-image").eq(index).show();
	});
	
	jQuery(".view-fiche-d-tail-choix-d-clinaison.view-display-id-block li").click(function(){
		changeDeclinaison(jQuery(this).index());
	});
	
	//Fonction pour changer la déclinaison sur une fiche produit
	function changeDeclinaison(index){
		//On change les images à droite
		jQuery(".view-fiche-produit-galerie-couleur .views-field-field-d-clinaisons li").hide();
		jQuery(".view-fiche-produit-galerie-couleur .views-field-field-d-clinaisons li").eq(index).show();
		//On change les produits associés
		jQuery(".view-id-fiche_d_tail_choix_d_clinaison.view-display-id-block_1 li").hide();
		jQuery(".view-id-fiche_d_tail_choix_d_clinaison.view-display-id-block_1 li").eq(index).show();
	}
	
	function getHash(){
		var hash = window.location.hash;
		return hash.substring(1);
	}
	
});

function resizeSlider(){
	var wHeight = jQuery(window).height();
	var wWidth = jQuery(window).width();
	console.log('hauteur : '+(wHeight-42));
	jQuery('#zone-menu-wrapper').css({'height':(wHeight-42)+'px'});
	//jQuery('#zone-header-wrapper').css({'height':(wHeight-42)+'px'});
}