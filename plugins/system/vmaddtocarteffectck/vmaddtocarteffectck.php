<?php

/**
 * @copyright	Copyright (C) 2011 Cédric KEIFLIN alias ced1870
 * http://www.joomlack.fr
 * @license		GNU/GPL
 * */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemvmaddtocarteffectck extends JPlugin {

    function plgSystemvmaddtocarteffectck(&$subject, $config) {
        parent :: __construct($subject, $config);
        
    }

    function onAfterRender() {

		// vérifie qu'on est dans VM
		if (JRequest::getVar('option') != 'com_virtuemart')
			return false;

        $app = & JFactory::getApplication();
        $document = & JFactory::getDocument();
        $doctype = $document->getType();

        // si pas en frontend, on sort
        if ($app->isAdmin()) {
            return false;
        }

        // si pas HTML, on sort
        if ($doctype !== 'html') {
            return;
        }
		
		// charge les parametres
		$duration = $this->params->get('duration', '500');
		$transition = $this->params->get('transition', 'linear');
		$offsetx = $this->params->get('offsetx', '10');
		$offsety = $this->params->get('offsety', '10');
		$productimageclass = $this->params->get('productimageclass', 'product-image'); // classe image dans page produit
		$categoryimageclass = $this->params->get('categoryimageclass', 'browseProductImage'); // classe image dans page categorie
		$categorycontainerclass = $this->params->get('categorycontainerclass', 'spacer'); // classe du conteneur image dans la page categorie
		
		// crée le script jquery
		$variable = "<script type=\"text/javascript\">
		jQuery(document).ready(function($) {
			var vmcartck = $('.vmCartModule');
			vmcartck.top = vmcartck.offset().top;
			vmcartck.left = vmcartck.offset().left;
			
			$('.addtocart-button').click(function() {
					var el = $(this);
					var imgtodrag = $('.".$productimageclass.":first');
					if (!imgtodrag.length) {
						elparent = el.parent();
						while (!elparent.hasClass('".$categorycontainerclass."')) {
							elparent = elparent.parent();
						}	
						imgtodrag = elparent.find('img.".$categoryimageclass."');
					}
					if (imgtodrag.length) {
						var imgclone = imgtodrag.clone()
							.offset({ top: imgtodrag.offset().top, left: imgtodrag.offset().left })
							.css({'opacity': '0.7', 'position': 'absolute'})
							.appendTo($('body'))
							.animate({
								'top': vmcartck.top+".$offsety.",
								'left': vmcartck.left+".$offsetx."
							},".$duration.", '".$transition."');
						imgclone.animate({
							'width': 0,
							'height': 0
						}, 500, function() {
							imgclone.remove();
						  });
					}
			});
		});
		</script>";
					
		// renvoie les données dans la page
        $body = JResponse::getBody();
        $body = str_replace('</head>', $variable . '</head>', $body);
        JResponse::setBody($body);
    }
}
?>