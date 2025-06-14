<!-- ReDi Restaurant Reservation plugin version <?php echo $this->version?> -->
<!-- Revision: 20250513 -->
<?php require_once(REDI_RESTAURANT_TEMPLATE.'cancel.php');?>
<?php require_once(REDI_RESTAURANT_TEMPLATE.'modify.php');?>
<script type="text/javascript">var plugin_name='ReDi Restaurant Reservation version <?php echo $this->version?>';var displayLeftSeats = <?php echo $displayLeftSeats ? 1 : 0; ?>;

var redirect_to_confirmation_page = '<?php echo $redirect_to_confirmation_page; ?>';var timepicker = '<?php echo $timepicker;?>';var date_format = '<?php echo $calendar_date_format ?>';var timepicker_time_format ='<?php echo $timepicker_time_format;?>';var locale = '<?php echo $js_locale?>';var datepicker_locale = '<?php echo $datepicker_locale?>'; var timeshiftmode = '<?php echo $timeshiftmode; ?>'; var hidesteps = <?php echo $hidesteps ? 1 : 0; ?>; var apikeyid = '<?php echo $apiKeyId; ?>'; var waitlist='<?php echo $waitlist; ?>'; var maxDate = new Date();maxDate.setFullYear(maxDate.getFullYear() + 1); var min_persons='<?php echo $minPersons; ?>'; var max_persons = '<?php echo $maxPersons; ?>'; var large_group_message = '<?php echo (!empty($largeGroupsMessage))? __( 'More than [max] people', 'redi-restaurant-reservation' ) : '' ?>';  </script>

<form id="redi-reservation" name="redi-reservation" method="post" action="?jquery_fail=true">
	<?php if (isset($EnableCancelForm) && $EnableCancelForm): ?>
	<a href="#cancel" id="cancel-reservation" class="cancel-reservation"><?php _e('Cancel reservation', 'redi-restaurant-reservation')?></a>
	<br>
	<?php endif ?>
	<?php if (isset($EnableModifyReservations) && $EnableModifyReservations): ?>
	<a href="#modify" id="modify-reservation" class="modify-reservation"><?php _e('Modify reservation', 'redi-restaurant-reservation')?></a>
	<br>
	<?php endif ?>
	<?php if (isset($EnableSocialLogin) && $EnableSocialLogin): ?>

	<?php if (function_exists('the_champ_social_login_enabled')): ?>
	<div id="social">
		<?php if($username):?>
<p>
		<?php _e( 'Welcome back', 'redi-restaurant-reservation' )?><b> <?php echo $username ?></b>
</p>
	<?php echo $userimg ?>
<br/>
<?php else: ?>
	<h4>
		<?php _e('Login with your social media account', 'redi-restaurant-reservation') ?>
		</h4>

	<?php $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>
				<?php echo do_shortcode('[TheChamp-Login redirect_url="' . $url . '"]') ?>
				<br/>
				<?php endif ?>
	</div>
	<?php else: ?>
		<div style="clear:both"></div>
		<p style="color:red"><?php _e('ERROR: You need to install/activate dependant plugin Super Socializer plugin', 'redi-restaurant-reservation') ?></p>
	<?php endif ?>

<?php endif ?>

	<div id="step1">
		
		<?php if ( count( (array) $places ) > 1 ): /* multiple places */ ?>
            <h4>
				<?php _e( 'Step', 'redi-restaurant-reservation' ) ?> 1: <?php _e( 'Select place, guests, date and time', 'redi-restaurant-reservation' ) ?>
			</h4>
			<label for="placeID">
				<?php _e( 'Place', 'redi-restaurant-reservation' ) ?>:</label>
			<select name="placeID" id="placeID" class="redi-reservation-select">
					<option value="0">
						<?php _e("Select place", 'redi-restaurant-reservation' ) ?>
					</option>
				<?php foreach((array)$places as $place_current):?>
					<option value="<?php echo $place_current->ID ?>" data-duration="<?php echo $place_current->ReservationDuration; ?>">
						<?php echo $place_current->Name ?>
					</option>
				<?php endforeach; ?>
			</select>
			

		<?php else: /* only one place */ ?>
            <div class="rowLeft">
            <h4>
				<?php _e( 'Step', 'redi-restaurant-reservation' ) ?> 1: <?php _e( 'Select guests, date and time', 'redi-restaurant-reservation' ) ?>

			</h4>
	</div>

	
         <input type="hidden" id="placeID" name="placeID" value="<?php echo $places[0]->ID ?>"/>
         <?php endif ?>
         <?php 
         $guestslable = (isset($childrenSelection) && $childrenSelection != 0) ? __('Adult guests', 'redi-restaurant-reservation') : __('Guests', 'redi-restaurant-reservation'); ?>
		<label for="persons">
		<?php _e($guestslable, 'redi-restaurant-reservation')?>:<span class="redi_required"> *</span></label>
		<select name="persons" id="persons" class="redi-reservation-select">
				<option value="0" selected="selected">
					<?php echo (isset($childrenSelection) && $childrenSelection != 0) ? 
						__('Number of adults', 'redi-restaurant-reservation') : 
						__('Number of guests', 'redi-restaurant-reservation') ?>
                </option>
			<?php for ($i = $minPersons; $i != $maxPersons+1; $i++): ?>
				<option value="<?php echo $i?>" >
                    <?php echo $i ?>
                </option>
			<?php endfor?>
            <?php if (!empty($largeGroupsMessage)):?>
                <option value="group" >
                    <?php 
					// Translators: %s is the number of guests.
					echo sprintf( __( 'More than %s Guests', 'redi-restaurant-reservation' ), $maxPersons );?>
                </option>
            <?php endif ?>
		</select>

		<?php if ($childrenSelection):?>
			<label for="children"><?php _e('Children', 'redi-restaurant-reservation')?>:<span class="redi_required"> *</span>
			<br/>
			<span class="children_description"><?php echo empty($childrenDescription) ? '': ' ' . $childrenDescription?></span></label>
			
			<select name="children" id="children" class="redi-reservation-select">
				<option value=""><?php _e('Number of children', 'redi-restaurant-reservation') ?></option>
				<?php $MaxChild = ($MaxChild != "") ? $MaxChild : 50;

				for ($i = 0; $i != $MaxChild+1; $i++): ?>
					<option value="<?php echo $i?>">
						<?php echo $i ?>
					</option>
				<?php endfor?>          
			</select>
			
		<?php endif ?>

		<div id='redi-date-block' style="display: none;">
		<label for="redi-restaurant-startDate"><?php _e('Date', 'redi-restaurant-reservation')?>:<span class="redi_required"> *</span></label>
		<?php if($calendar === 'show'): ?>
			<div id="redi-restaurant-startDate" class="notranslate" style="display: none;"></div>
		<?php else: ?>
		<input type="text" name="startDate" id="redi-restaurant-startDate" style="display: none;"/>
		<?php endif ?>
		<img id="date_info_load" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
		<input id="redi-restaurant-startDateISO" type="hidden" value="<?php echo $startDateISO ?>" name="startDateISO"/>
		</div>

		<?php if(!$hide_clock):?>
        <label for="redi-restaurant-startHour"><?php _e('Time', 'redi-restaurant-reservation')?>:<span class="redi_required"> *</span></label>
		<?php if (isset($timepicker) && $timepicker === 'dropdown'):?>
			<select id="redi-restaurant-startHour" class="redi-reservation-select">
				<?php foreach(range(0, 23) as $hour):?>
					<option value="<?php echo $hour;?>" <?php if(gmdate('H',$startTime)==$hour):?>selected="selected"<?php endif;?>><?php echo gmdate($time_format_hours, strtotime( $hour.':00'));?></option>
				<?php endforeach;?>
			</select>&nbsp;:&nbsp;<select id="redi-restaurant-startMinute" class="redi-reservation-select">
				<?php foreach(range(0, 45, 15) as $minute):?>
					<option value="<?php printf('%02d', $minute);?>"><?php printf('%02d', $minute);?></option>
				<?php endforeach;?>
			</select>
			<input id="redi-restaurant-startTime-alt" type="hidden" value="<?php echo date_i18n('H:i', $startTime);?>" name="startTime"/>
		<?php else:?>
				<input id="redi-restaurant-startTime-alt" type="hidden" value="<?php echo date_i18n('H:i', $startTime);?>"/>
			<input id="redi-restaurant-startTime" type="text" value="<?php echo date_i18n($time_format, $startTime);?>" name="startTime"/>
		<?php endif ?>
		<?php endif;?>
		
		<?php if(isset($start_time_array)):?>
			<input id="redi-restaurant-startTimeArray" type="hidden" name="StartTimeArray" value="<?php echo $start_time_array; ?>" />
		<?php endif;?>

			<?php $default_duration = $default_reservation_duration;?>
			<?php if (isset($custom_duration)):?>

        <label><?php _e('Duration', 'redi-restaurant-reservation')?>:</label>
      <p>
			<?php foreach ($custom_duration["durations"] as $duration):?>
				<button class="redi-restaurant-duration-button button"
				value="<?php echo $duration["duration"] ?>" 
				<?php if($duration["duration"] == $default_reservation_duration):?>
				select="select"
				<?php endif;?>
				><?php echo $duration["name"] ?></button>
		<?php endforeach;?>
		</p>
		<?php endif;?>
	  
	  		<input type="hidden" id="duration" value="<?php echo $default_duration ?>"/>
 
		<?php if ( $timeshiftmode === 'byshifts' || $hidesteps): ?>

			<div id="step1times">
		        <span id="step1buttons"></span>
	        </div>
		<?php else: /* byshifts end */?>
			<?php $all_busy = false; ?>
		<div class="redi-restaurant-button-wrapper">
		        <?php if($timeshiftmode != 'byshifts'):?>
					<input class="redi-restaurant-button" id="step1button"style="display:none;" type="submit" value="<?php _e('Check available time', 'redi-restaurant-reservation');?>" name="submit">
		        <?php endif?>
		    </div>
		<?php endif /* normal */ ?>
        <div id="large_groups_message" style="display: none;" class="redi-reservation-alert-info redi-reservation-alert"><?php echo $largeGroupsMessage?></div>

		<div>
			<img id="step1load" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
		</div>

		
	</div>
		
		<div id="step1busy" <?php if(!$all_busy):?>style="display: none;"<?php endif; ?> class="redi-reservation-alert-error redi-reservation-alert">
			<?php 
				_e('Reservation is not available on selected day. Please select another day.', 'redi-restaurant-reservation');
				
				if ($waitlist) 
				{
					echo('<div name="message-waitlist-form">');
					_e('In case you would like to be notified when someone cancels reservation for this day, please fill <a class="link-waitlist-form">this form</a>.', 'redi-restaurant-reservation');
					echo('</div>');
				}
			?>
			<?php ?>
		</div>
		<div id="step1errors" style="display: none;" class="redi-reservation-alert-error redi-reservation-alert">
		<?php 
			if (isset($step1['Error'])):
				echo $step1['Error'];
			endif;
		?>
		</div>

	<div id="step2" <?php if ($timeshiftmode !=='byshifts' || $hidesteps): ?>style="display: none" <?php endif ?>>

		<?php if ( $timeshiftmode !=='byshifts' || $hidesteps ): ?>
            <h4>
				<?php _e('Step', 'redi-restaurant-reservation')?> 2: <?php _e('Select available time', 'redi-restaurant-reservation')?>
			</h4>
		<?php endif ?>
		
		<?php if ( $timeshiftmode ==='byshifts'){ ?>
        
        <span id="time2label" style="display: none"><label><?php _e('Time', 'redi-restaurant-reservation')?>:</label>
        </span>
        <?php }?>
		<div id="buttons" class="buttons-wrapper"></div>
		<input type="hidden" id="redi-restaurant-startTimeHidden" value=""/>
		<input type="hidden" id="redi-restaurant-duration" value=""/>
        <img id="step2load" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
        <div id="step2errors" style="display: none;" class="redi-reservation-alert-error redi-reservation-alert"></div>

		<?php if ($hidesteps):?>
			<input class="redi-restaurant-button button" type="submit" id="step2prev" value="<?php _e('Previous', 'redi-restaurant-reservation')?>">
		<?php endif ?>
		<div style="clear:both"></div>
	</div>

	<div id="step3" style="display: none;">
        <h4>
			<?php _e( 'Step', 'redi-restaurant-reservation' ) ?> <?php echo ( $timeshiftmode !=='byshifts' || $hidesteps ) ? 3 : 2 ?>: <?php _e( 'Provide reservation details', 'redi-restaurant-reservation' ) ?>
		</h4>
        <div id="redi-form-fields-container">
        <div data-display-order="-49" id="returned_user" <?php if(!$returned_user):?>style="display:none"<?php endif?>>
            <div>
                <a id="notyou" href="" style="float: right;"> <?php _e('Change details','redi-restaurant-reservation');?></a>
            </div>
            <div>
                <div><?php _e('Name', 'redi-restaurant-reservation');?>:</div><?php echo $username ?><br/><br/>
            </div>
            <div>
                <div><?php _e('Phone', 'redi-restaurant-reservation');?>:</div><?php echo $phone ?><br/><br/>
            </div>
            <div>
                <div><?php _e('Email', 'redi-restaurant-reservation');?>:</div><?php echo $email ?><br/><br/>
            </div>
        </div>	
        <div id="name_phone_email_form" <?php if($returned_user):?> style="display:none"<?php endif?>>

			<div data-display-order="-39">

				<?php if($enablefirstlastname == 'true'){ ?>
				<label for="UserName"><?php _e('First Name', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span></label>
				<?php }else{ ?>
				<label for="UserName"><?php _e('Name', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span>
				</label>
				<?php } ?>
				<input type="text" value="<?php echo $username;?>" name="UserName" id="UserName" autocomplete="name" >
			</div>
			<?php if($enablefirstlastname == 'true'){ ?>
			<div data-display-order="-29" >
				<label for="UserLastName"><?php _e('Last Name', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span>
				</label>
				<input type="text" value="<?php echo $lname;?>" name="UserLastName" id="UserLastName" autocomplete="name">
			</div>
			<?php } ?>
			<div data-display-order="-19">
				<label for="intlTel"><?php _e('Phone', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span></label>
				<input type="text" value="" name="intlTel" id="intlTel">
			</div>
			<div>		
				<input type="hidden" value="<?php echo $phone ?>" name="UserPhone" id="UserPhone">
			</div>
			<div data-display-order="-9">
				<label for="UserEmail"><?php _e('Email', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span>
				</label>
				<input type="text" value="<?php echo $email ?>" name="UserEmail" id="UserEmail" autocomplete="email" >
			</div>
		</div>

		<!-- custom fields -->
		<img id="RediCustomFields" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
		
		<div id="custom_fields_container"></div>
		<!-- /custom fields -->  
			 
		<?php if( $ShowComment === 1 || $ShowComment === '1' || !isset($ShowComment) ): ?>		
   
			<div data-display-order="50">
				<label for="UserComments">
					<?php _e('Comment', 'redi-restaurant-reservation');?>:
				</label>
				<textarea maxlength="250" rows="5" name="UserComments" id="UserComments" cols="20" class="UserComments"></textarea>
			</div>
		<?php endif; ?>
		</div>
		

		<?php if ($captcha):?>			
			<div id="redi-captcha" class="g-recaptcha" data-sitekey="<?php echo $captchaKey ?>"></div>
		<?php endif ?>

		<div>
			<?php if ($hidesteps):?>
				<input class="redi-restaurant-button button" type="submit" id="step3prev" value="<?php _e('Previous', 'redi-restaurant-reservation')?>">
			<?php endif ?>
			<input class="redi-restaurant-button button" type="submit" id="redi-restaurant-step3" name="action" value="<?php 
			if (isset($manual) && $manual): 
				_e('Make a reservation request', 'redi-restaurant-reservation'); 
			else: 
				_e('Make a reservation', 'redi-restaurant-reservation'); endif?>">
			<img id="step3load" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
		</div>
		<div id="step3errors" style="display: none;" class="redi-reservation-alert-error redi-reservation-alert"></div>
	</div>
	<div id="step4" style="display: none;" class="redi-reservation-alert-success redi-reservation-alert">
		<h4><strong>
			<?php if (isset($manual) && $manual):
				_e('Thank you for your reservation request.', 'redi-restaurant-reservation');
			else:
				_e('Thank you for your reservation.', 'redi-restaurant-reservation');
			endif;?>
		</strong>
        </h4>
        <div>
		<?php if (isset($manual) && $manual):?>
			<?php _e('We wish to inform you that your booking request has been received. We are checking availability and will get back to you as soon as possible. Please wait confirmation of your reservation.', 'redi-restaurant-reservation');?>
		<?php else:?>
			<?php _e('A confirmation email has been sent to you, should you not receive it, please rest assured that your booking has been received and is confirmed. If you wish you may contact us by phone to confirm.', 'redi-restaurant-reservation');?>
		<?php endif?>
        </div>
        <br/>
        <?php if (isset($manual) && $manual):
			_e('Your reservation request number for reference:', 'redi-restaurant-reservation');
		else:
			_e('Your reservation number for reference:', 'redi-restaurant-reservation');
		endif; ?> <span id="reservation-id" style="font-weight: bold"></span>
    </div>
		<?php 

		if (isset($step1["all_booked_for_this_duration"])):?>
			<div id="step2busy" class="redi-reservation-alert-error redi-reservation-alert">
				
				<?php if (empty($fullyBookedMessage)):
					_e('There are no more reservation can be made for this day.', 'redi-restaurant-reservation');
				else:
					echo $fullyBookedMessage;
				endif?>

			</div>
		<?php elseif ($waitlist == "specific_time" && !isset($step1["all_booked_for_this_duration"]) ): ?>
			<div id="step2busy" <?php if(!$all_busy):?>style="display: none;clear:both"<?php endif; ?> class="redi-reservation-alert-error redi-reservation-alert">
				<?php if (empty($fullyBookedMessage)):
					_e('There are no more reservation can be made for this time.', 'redi-restaurant-reservation');
				else:
					echo $fullyBookedMessage;
				endif?>

				<div name="message-waitlist-form">
					<?php _e('In case you would like to be notified when someone cancels reservation for this time, please fill <a class="link-waitlist-form">this form</a>.', 'redi-restaurant-reservation');?>
				</div>
			</div>
		<?php elseif (!isset($step1["all_booked_for_this_duration"]) AND $waitlist): ?>
			<div id="step2busy" <?php if(!$all_busy):?>style="display: none;clear:both"<?php endif; ?> class="redi-reservation-alert-error redi-reservation-alert">
				<?php if (empty($fullyBookedMessage)):
					_e('There are no more reservation can be made for this day.', 'redi-restaurant-reservation');
				else:
					echo $fullyBookedMessage;
				endif?>

				<div name="message-waitlist-form">
					<?php _e('In case you would like to be notified when someone cancels reservation for this day, please fill <a class="link-waitlist-form">this form</a>.', 'redi-restaurant-reservation');?>
				</div>
			</div>

		<?php else: ?>

		<div id="step2busy" <?php if(!$all_busy):?>style="display: none;"<?php endif; ?> class="not-waitlist redi-reservation-alert-error redi-reservation-alert">
			<?php _e('Reservation is not available on selected day. Please select another day.', 'redi-restaurant-reservation');?>
		</div>

	<?php endif; ?>
</form>
<?php 
if( $userfeedback == 'true') {?>
    <form class="userfeedback" action="#" style="display: none;">
    	<div id="errors" style="display: none;" class="redi-reservation-alert-error redi-reservation-alert"></div>
    	<div id="sucess" style="display: none;" class="redi-reservation-alert-success redi-reservation-alert"></div>
    	<div class="field_row">
    		<span>Rate your reservation experience</span>
    		<div class="radio">
			  	<label>
				    <input type="radio" name="stars" value="1" />
				    <span class="icon">★</span>
			  	</label>
			  	<label>
				    <input type="radio" name="stars" value="2" />
				    <span class="icon">★</span>
				    <span class="icon">★</span>
			  	</label>
			  	<label>
				    <input type="radio" name="stars" value="3" />
				    <span class="icon">★</span>
				    <span class="icon">★</span>
				    <span class="icon">★</span>   
				</label>
				<label>
			    	<input type="radio" name="stars" value="4" />
				    <span class="icon">★</span>
				    <span class="icon">★</span>
				    <span class="icon">★</span>
				    <span class="icon">★</span>
			  	</label>
			  	<label>
				    <input type="radio" name="stars" value="5" checked/>
				    <span class="icon">★</span>
				    <span class="icon">★</span>
				    <span class="icon">★</span>
				    <span class="icon">★</span>
				    <span class="icon">★</span>
			  	</label>
			</div>
		</div>
	  	<div class="field_row">
	  		<span><?php _e("What we can do better?", 'redi-restaurant-reservation') ?><span class="req_start"></span></span>
	  		<div>
				<textarea name="comment"></textarea>
			</div>
	  	</div>
	  	<div class="field_row">
	  		<input type="submit" name="submit" value="Send">
	  	</div>
	</form>
<?php } ?>
<div class="waitlist-form" style="display: none;">
	<h4><?php _e( 'Wait List', 'redi-restaurant-reservation' ) ?></h4>
		<form name="redi-waitlist-form" id="redi-waitlist-form" action="POST">
			<input type="hidden" id="waitlist-placeID" name="placeID" value="<?php echo $places[0]->ID ?>"/>
		 	<div>
		 		<label for="redi-restaurant-startDate"><?php _e('Date', 'redi-restaurant-reservation')?>: <b><span id="waitlist-startDate-label"></span></b></label>
				<input type="hidden" value="<?php echo $startDate ?>" name="waitlist-startDate" id="redi-waitlist-startDate"/>
		 	</div>
		 	<div>
		 		<label for="waitlist-persons">
				<?php _e('Persons', 'redi-restaurant-reservation')?>: <b><span id="waitlist-persons-label"></span></b></label>
				<input type="hidden" name="waitlist-persons" id="waitlist-persons">
		 	</div>
		 	<div>
		 		<label for="waitlist-Time">
				<?php _e('Preferred time', 'redi-restaurant-reservation')?>: <b><span id="waitlist-startDatetime-label"></span></b></label>
				<input type="text" value="" name="waitlist-Time" id="waitlist-Time">
		 	</div>
		    <div>
		        <label for="waitlist-UserName"><?php _e('Name', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span>
		        </label>
		        <input type="text" value="" name="waitlist-UserName" id="waitlist-UserName" autocomplete="name">
		    </div>
		    <div>
		        <label for="waitlist-UserEmail"><?php _e('Email', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span>
		        </label>
		        <input type="text" value="" name="waitlist-UserEmail" id="waitlist-UserEmail" autocomplete="email">
		    </div>
		    <div>
		        <label for="waitlist-intlTel"><?php _e('Phone', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span></label>
				<input type="text" value="" name="waitlist-intlTel" id="waitlist-intlTel">
				<div>
					<input type="hidden" value="" name="waitlist-UserPhone" id="waitlist-UserPhone">
				</div>
			</div>

			<!-- custom fields -->
			<?php foreach ( $custom_fields as $custom_field )
			{
				if ($custom_field->Type == 'gdpr')
				{ 
					$input_field_type = 'checkbox';
			?>
				<div>
					<label for="waitlist_field_<?php echo $custom_field->Id; ?>"><?php echo $custom_field->Name; ?>:
						<?php if(isset($custom_field->Required) && $custom_field->Required):?><span class="redi_required"> *</span>
							<input type="hidden" id="<?php echo 'waitlist_field_'.$custom_field->Id.'_message'; ?>" value="<?php echo !empty($custom_field->Message) ? $custom_field->Message : _e('Custom field is required', 'redi-restaurant-reservation');?>">
						<?php endif;?>
					</label>
					<input type="<?php echo($input_field_type);?>" value="" id="waitlist_field_<?php echo($custom_field->Id);?>" name="waitlist_field_<?php echo($custom_field->Id);?>" <?php if(isset($custom_field->Required) && $custom_field->Required):?>class="waitlist_field_required"<?php endif; ?>>
				</div>
			<?php 
				}
			} ?>
			<!-- /custom fields -->        


			<div>
		    	<input class="redi-restaurant-button button" type="submit" id="redi-waitlist-submit" name="action" value="<?php _e('Register to Wait List', 'redi-restaurant-reservation')?>">
		    </div>			
		</form>
		<div>
			<img id="waitlistload" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
		</div>
		<div id="wait-list-error" style="display: none;" class="redi-reservation-alert-error redi-reservation-alert">
		</div>
		<div id="wait-list-success" style="display: none;" class="redi-reservation-alert-success redi-reservation-alert">
			<?php _e( 'Your information has been saved successfully and you will be notified once there are available seats for requested date.', 'redi-restaurant-reservation' ) ?>
			
	    </div>
</div>

<?php if($thanks):?>
	<div id="Thanks" style="">
		<a style="float: right;" href="https://reservationdiary.eu" target="_blank">
			<label style="font-size: 10px;">
			<?php _e('Powered by', 'redi-restaurant-reservation')?>
			</label>
			<img style="border:none; margin-left: 3px;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL?>img/logo.png" alt="Powered by ReservationDiary.eu" title="Powered by ReservationDiary.eu"/></a>
	</div>
<?php endif ?>