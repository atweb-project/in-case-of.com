<?php
/**
 * @package     Komento
 * @copyright   Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>

dispatch("Foundry/2.1").to(function($, manifest) {

	$.Component(
		'Komento',
		{
			baseUrl: '<?php echo $url; ?>',
			environment: '<?php echo $environment; ?>',
			version: '<?php echo KomentoVersionHelper::getLocalVersion(); ?>',
			jversion: '<?php echo Komento::joomlaVersion(); ?>',
			spinner: $.rootPath + 'media/com_komento/images/loader.gif',
			view: '<?php echo JRequest::getVar( "view", "" ); ?>',
			config: <?php echo json_encode($config->toObject()); ?>,
			konfig: <?php echo json_encode($konfig->toObject()); ?>,
			acl: <?php echo isset($acl) ? json_encode($acl) : 0; ?>,
			guest: <?php echo isset($guest) ? $guest : 0; ?>,
			ajax: {
				data: {
					"<?php echo Komento::_( 'getToken' ); ?>" : 1
				}
			},
			element: {}
		},
		function(self)
		{
			if(Komento.environment == "development")
			{
				try {
					console.info('Komento component is now ready');
				} catch(err) {}
			}
		}
	);
});
