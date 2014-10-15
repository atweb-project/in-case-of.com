<?php
/**
 * @autor       Valentín García
 * @website     www.valentingarcia.com.mx
 * @package		Joomla.Site
 * @subpackage	mod_mypricing
 * @copyright	Copyright (C) 2013 Valentín García. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//set vars for css classes
$plan_classes = array(
	'plan1',
	'plan2',
	'plan3',
	'plan4'
);
//set width for plans
/*$plan_total = $status[0] + $status[1] + $status[2] + $status[3];
switch( $plan_total ){
	default:
	case 4:
		$plan_widths = ' style="width:25%"';
	break;
	case 3:
		$plan_widths = ' style="width:33.3%"';
	break;
	case 2:
		$plan_widths = ' style="width:49%"';
	break;
	case 1:
		$plan_widths = ' style="width:100%"';
	break;
}*/
 
//counter
$vg_count_ = 0;
?>

<div class="entry">

	<!-- plans -->
	<div class="pricetable">
		
		<?php foreach( $plan_classes as $plan_class ){ //<-- A1. ?>
		
			<?php if( $status[$vg_count_] == 1 ){ //<-- A2. ?>
			
			<!-- plan -->
			<div class="plan <?php echo $plan_class; ?>">
			
				<?php if( $names[$vg_count_] || $prices[$vg_count_] ){ //<-- A4. ?>
					<figure>
					
						<?php if( $names[$vg_count_] ){ ?>
							<figcaption><?php echo $names[$vg_count_]; ?></figcaption>
						<?php } ?>
					
						<?php if( $prices[$vg_count_] ){ ?>
							<div class="price"><?php echo JText::_('VG_MP_PRICE_SIGN'); ?><span><?php echo $prices[$vg_count_]; ?></span> </div>
						<?php } ?>
					
					</figure>
				<?php } //.A4 --> ?>
				
				<?php if( $contents[$vg_count_] ){//<-- A3. ?>
				
					<?php
					//separate from text1::text2::text3 to get something like text1<breakline>text2<breakline>...
					$contents_ = explode('::', $contents[$vg_count_]);
					?>
					<ul>
						
						<?php
						foreach( $contents_ as $content_ ){
							echo '<li>' . $content_ . '</li>';
						}
						?>
						
					</ul>
				<?php } //.A3 --> ?>
				
				<?php if( $txtlinks[$vg_count_] && $links[$vg_count_] ){ ?>
					<footer>
						<a href="<?php echo $links[$vg_count_]; ?>" target="_self" class="built-in-btn"><?php echo $txtlinks[$vg_count_]; ?></a>
					</footer>
				<?php } ?>
				
			</div>
			<!-- /plan -->
			
			<?php } //.A2 --> ?>
		
			<?php $vg_count_++;//autoincrement ?>
			
		<?php } //.A1 --> ?>
		
	</div>
	<!-- /plans -->

</div>

<?php $vg_count_ = null; //just reset ?>