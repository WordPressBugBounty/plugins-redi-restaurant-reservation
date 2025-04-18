<div id="modify-reservation-div" style="display: none">
    <h4><?php _e('Modify reservation', 'redi-restaurant-reservation')?></h4>
    <a href="#modify" class="back-to-reservation modify-reservation"><?php _e('Back to reservation page', 'redi-restaurant-reservation')?></a>
	<div id="modify-reservation-form">
		<form method="post" action="?jquery_fail=true">
			<label for="redi-restaurant-modifyID"><?php _e('Reservation number', 'redi-restaurant-reservation')?>:<span class="redi_required"> *</span></label>
			<input type="text" name="modifyID" id="redi-restaurant-modifyID"/>
			<label for="modifyPhone-intlTel"><?php _e('Phone', 'redi-restaurant-reservation')?>:</label>
			<input type="text" value="" name="modifyPhone-intlTel" id="modifyPhone-intlTel">
			<input type="hidden" name="redi-restaurant-modifyPhone" id="redi-restaurant-modifyPhone"/>
			<label for="redi-restaurant-modifyName"><?php _e('Name', 'redi-restaurant-reservation')?>:</label>
			<input type="text" name="modifyName" id="redi-restaurant-modifyName"/>
			<label for="redi-restaurant-modifyEmail"><?php _e('Email', 'redi-restaurant-reservation')?>:</label>
			<input type="text" name="modifyEmail" id="redi-restaurant-modifyEmail"/>
            <input class="redi-restaurant-button" type="submit" id="redi-restaurant-modify" name="action" value="<?php _e('Find reservation', 'redi-restaurant-reservation')?>">
            <img id="modify-load" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
		</form>
	</div>
    <div id="modify-errors" style="display: none;" class="redi-reservation-alert-error redi-reservation-alert"></div>
</div>

<div id="update-reservation-div" style="display: none">
    <h4><?php _e('Update reservation', 'redi-restaurant-reservation')?></h4>
    <a href="#modify" class="back-to-reservation modify-reservation"><?php _e('Back to reservation page', 'redi-restaurant-reservation')?></a>
	<br/>
	<div id="update-reservation-form">
		<form method="post" action="?jquery_fail=true">		
			<div class="redi-modify-date-time">
				<?php _e('Date', 'redi-restaurant-reservation')?>: <div class="redi-modify-date" id="updateDateFrom"></div>
			</div>
			<div class="redi-modify-date-time">
				<?php _e('Time', 'redi-restaurant-reservation')?>: <div class="redi-modify-time" id="updateTimeFrom"></div>
			</div>

				<label for="updatePersons">
				<?php _e('Number of guests', 'redi-restaurant-reservation') ?>
				</label>
				<select name="updatePersons" id="updatePersons" class="redi-reservation-select">
					<?php for ($i = $minPersons; $i != $maxPersons+1; $i++): ?>
						<option value="<?php echo $i?>" >
							<?php echo $i ?>
						</option>
					<?php endfor?>
				</select>
			<?php /* if ($childrenSelection):?>
				<label for="ChildrenQuantity"><?php _e('Children', 'redi-restaurant-reservation')?>:
					<br/>
			<span class="children_description"><?php echo empty($childrenDescription) ? '': ' ' . $childrenDescription?></span></label>
				<select name="ChildrenQuantity" id="ChildrenQuantity" class="redi-reservation-select">
					<?php $MaxChild = ($MaxChild != "") ? $MaxChild : 50;
					for ($i = 0; $i != $MaxChild+1; $i++): ?>
						<option value="<?php echo $i?>">
							<?php echo $i ?>
						</option>
					<?php endfor?>          
				</select>
			
		<?php endif */ ?>
			<div>
				<label for="updateUserName"><?php _e('Name', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span></label>
				<input type="text" value="" name="updateUserName" id="updateUserName">
			</div>
			<div>
				<label for="updateUserPhone-intlTel"><?php _e('Phone', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span></label>		
				<input type="text" value="" name="updateUserPhone-intlTel" id="updateUserPhone-intlTel">
				<div>
					<input type="hidden" value="" name="updateUserPhone" id="updateUserPhone">
				</div>
			</div>
			<div>
				<label for="updateUserEmail"><?php _e('Email', 'redi-restaurant-reservation');?>:<span class="redi_required"> *</span></label>
				<input type="text" value="" name="updateUserEmail" id="updateUserEmail" >
			</div>
			<div>
				<label for="updateUserComments"><?php _e('Comment', 'redi-restaurant-reservation');?>:</label>
				<textarea maxlength="250" rows="5" name="updateUserComments" id="updateUserComments" cols="20" class="UserComments"></textarea>
			</div>
			<input type="hidden" value="" name="redi-restaurant-updateID" id="redi-restaurant-updateID">
			<input type="hidden" value="" name="updateTo" id="updateTo">
			<input type="hidden" value="" name="updateFrom" id="updateFrom">
			<input type="hidden" value="" name="updatePlaceReferenceId" id="updatePlaceReferenceId">		
			<input class="redi-restaurant-button" type="submit" id="redi-restaurant-update" name="action" value="<?php _e('Update reservation', 'redi-restaurant-reservation')?>">
			<img id="update-load" style="display: none;" src="<?php echo REDI_RESTAURANT_PLUGIN_URL ?>img/ajax-loader.gif" alt=""/>
		</form>
	</div>
	<div id="update-errors" style="display: none;" class="redi-reservation-alert-error redi-reservation-alert"></div>
    <div id="update-success" style="display: none;" class="redi-reservation-alert-success redi-reservation-alert">
		<strong>
			<?php _e( 'Reservation has been successfully updated.', 'redi-restaurant-reservation' ); ?>
		</strong>
	</div>	
</div>