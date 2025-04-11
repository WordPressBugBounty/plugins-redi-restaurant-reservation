<!-- ReDi Restaurant Reservation plugin version <?php echo $this->version ?> -->



<?php // require_once(REDI_RESTAURANT_TEMPLATE . 'cancel.php'); 

?>

<?php // require_once(REDI_RESTAURANT_TEMPLATE . 'modify.php'); 

?>

<script type="text/javascript">
	var plugin_name = 'ReDi Restaurant Reservation version <?php echo $this->version ?>';

	var displayLeftSeats = <?php echo $displayLeftSeats ? 1 : 0; ?>;

	var redirect_to_confirmation_page = '<?php echo $redirect_to_confirmation_page; ?>';

	var timepicker = '<?php echo $timepicker; ?>';

	var date_format = '<?php echo $calendar_date_format ?>';

	var timepicker_time_format = '<?php echo $timepicker_time_format; ?>';

	var locale = '<?php echo $js_locale ?>';

	var datepicker_locale = '<?php echo $datepicker_locale ?>';

	var timeshiftmode = '<?php echo $timeshiftmode; ?>';

	var hidesteps = <?php echo $hidesteps ? 1 : 0; ?>;

	var apikeyid = '<?php echo $apiKeyId; ?>';

	var waitlist = '<?php echo $waitlist; ?>';

	var maxDate = new Date();

	maxDate.setFullYear(maxDate.getFullYear() + 1);

	var min_persons = '<?php echo $minPersons; ?>';

	var max_persons = '<?php echo $maxPersons; ?>';

	var large_group_message = '<?php echo (!empty($largeGroupsMessage)) ? __('More than [max] people', 'redi-restaurant-reservation') : '' ?>';
</script>



<?php $range = ($minPersons / $maxPersons) * 100; ?>



<div class="redi-app">

	<div class="redi-popup-container"></div>

	<section class="redi-hero redi-hero--overlay" style="background-image: url(<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/dev/hero.jpg);">

		<div class="redi-container">

			<div class="redi-wrapper">

				<div class="redi-hero__content">

					<h1 class="redi-h1"><?php _e('Reservation Form', 'redi-restaurant-reservation') ?></h1>

				</div>

			</div>

		</div>

	</section>

	<section class="redi-layout">

		<div class="redi-container">

			<div class="redi-wrapper">

				<div class="redi-layout__route redi-route active" data-redi-route="redi_step_form">

					<div class="redi-step-form">

						<div class="redi-step-form__main">

							<div class="redi-step-form__head active">

								<div class="redi-steps-indicator" data-redi-current-step="1">

									<button class="redi-steps-indicator__step redi-step-form__nav-button" data-redi-step="4">

										<div class="redi-steps-indicator__circle">4</div>

										<div class="redi-steps-indicator__title"><?php _e('Done!', 'redi-restaurant-reservation') ?></div>

									</button>



									<button class="redi-steps-indicator__step redi-step-form__nav-button" data-redi-step="3">

										<div class="redi-steps-indicator__circle">3</div>

										<div class="redi-steps-indicator__title"><?php _e('Details', 'redi-restaurant-reservation') ?></div>

									</button>



									<button class="redi-steps-indicator__step redi-step-form__nav-button" data-redi-step="2">

										<div class="redi-steps-indicator__circle">2</div>

										<div class="redi-steps-indicator__title"><?php _e('Time', 'redi-restaurant-reservation') ?></div>

									</button>



									<button class="redi-steps-indicator__step redi-step-form__nav-button active" data-redi-step="1">

										<div class="redi-steps-indicator__circle">1</div>

										<div class="redi-steps-indicator__title"><?php _e('Guests', 'redi-restaurant-reservation') ?></div>

									</button>

								</div>

							</div>



							<div class="redi-step-form__body">

								<div class="redi-step-form__step active" data-redi-step="1">



									<?php if ($largeGroupsMessage) : ?>

										<div class="redi-message redi-message--hidden" id="range_alert_message">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#attention"></use>

											</svg>

											<p class="redi-paragraph"><?php echo $largeGroupsMessage ?></p>

										</div>

									<?php endif; ?>

									<?php if (isset($EnableSocialLogin) && $EnableSocialLogin) : ?>



										<?php if (function_exists('the_champ_social_login_enabled')) : ?>

											<div id="social">

												<?php if ($username) : ?>

													<p>

														<?php _e('Welcome back', 'redi-restaurant-reservation') ?><b> <?php echo $username ?></b>

													</p>

													<?php echo $userimg ?>

													<br />

												<?php else : ?>

													<h4>

														<?php _e('Login with your social media account', 'redi-restaurant-reservation') ?>

													</h4>



													<?php $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>

													<?php echo do_shortcode('[TheChamp-Login redirect_url="' . $url . '"]') ?>

													<br />

												<?php endif ?>

											</div>

										<?php else : ?>

											<div style="clear:both"></div>

											<p style="color:red"><?php _e('ERROR: You need to install/activate dependant plugin Super Socializer plugin', 'redi-restaurant-reservation') ?></p>

										<?php endif ?>



									<?php endif ?>



									<div class="redi-place-select">

										<div class="redi-place-select__head">

											<h5 class="redi-h5 redi-place-select__title"><?php _e('Place', 'redi-restaurant-reservation') ?></h5>

										</div>



										<div class="redi-place-select__body" tabindex="0" value="Star Burger #1">

											<select id="place_select" form="redi_step_form" name="redi_place">

												<?php foreach ((array)$places as $place_current) : ?>

													<option value="<?php echo $place_current->ID ?>" data-address="<?php echo $place_current->Address ?>" data-title="<?php echo $place_current->Name ?>">

														<?php echo $place_current->Name ?>

													</option>

												<?php endforeach; ?>

											</select>

										</div>

									</div>



									<div class="redi-ranges">

										<!-- [data-redi-max] is optional attribute -->

										<div class="redi-range redi-range--persons" data-redi-min="<?php echo $minPersons; ?>" data-redi-max="<?php echo $maxPersons; ?>">

											<div class="redi-range__head">

												<h5 class="redi-h5 redi-range__title"><?php _e('Guests', 'redi-restaurant-reservation') ?></h5>

											</div>



											<div class="redi-range__body">

												<div class="redi-range__slider">

													<div class="redi-range__labels">

														<div class="redi-range__label active"><?php echo $minPersons; ?></div>

														<div class="redi-range__label"><?php echo $maxPersons; ?></div>

													</div>



													<div class="redi-range__inner">

														<div class="redi-range__line">

															<div class="redi-range__progress" style="width: <?php // echo $range 
																																							?>%;"></div>

															<input type="range" class="redi-range__range-input" min="<?php echo $minPersons; ?>" max="<?php echo $maxPersons; ?>" value="0" data-value="<?php echo $minPersons; ?>" form="redi_step_form" name="redi_persons">

														</div>

													</div>

												</div>



												<span class="redi-separator"><?php _e('OR', 'redi-restaurant-reservation') ?></span>



												<div class="redi-range__field">

													<input id="persons" type="text" class="redi-range__input" name="redi_persons" form="redi_step_form" min="<?php echo $minPersons; ?>" value="<?php echo $minPersons; ?>">

													<div class="redi-range__buttons">

														<button class="redi-range__field-button redi-range__increment-button">

															<svg class="redi-icon">

																<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#chevron-up"></use>

															</svg>

														</button>

														<button class="redi-range__field-button redi-range__decrement-button">

															<svg class="redi-icon">

																<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#chevron-down"></use>

															</svg>

														</button>

													</div>

												</div>

											</div>

										</div>



										<!-- [data-redi-max] is optional attribute -->

										<?php if ($childrenSelection) : ?>

											<div class="redi-range" data-redi-max="<?php echo $maxPersons; ?>" data-redi-min="0">

												<div class="redi-range__head">

													<h5 class="redi-h5 redi-range__title"><?php _e('Children', 'redi-restaurant-reservation') ?></h5>

													<div class="redi-range__message"><?php echo empty($childrenDescription) ? '' : ' ' . $childrenDescription ?><span class="children_description">

													</div>

												</div>



												<div class="redi-range__body">

													<div class="redi-range__slider">

														<div class="redi-range__labels">

															<div class="redi-range__label active">0</div>

															<div class="redi-range__label"><?php echo $maxPersons; ?></div>

														</div>



														<div class="redi-range__inner">

															<div class="redi-range__line">

																<div class="redi-range__progress"></div>

																<input type="range" class="redi-range__range-input" min="0" max="<?php echo $maxPersons; ?>" value="0" data-value="<?php echo $minPersons; ?>" form="redi_step_form" name="redi_children">

															</div>

														</div>

													</div>



													<span class="redi-separator"><?php _e('OR', 'redi-restaurant-reservation') ?></span>



													<div class="redi-range__field">

														<input id="children" name="redi_children" type="text" class="redi-range__input" value="0" form="redi_step_form">



														<div class="redi-range__buttons">

															<button class="redi-range__field-button redi-range__increment-button">

																<svg class="redi-icon">

																	<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#chevron-up"></use>

																</svg>

															</button>

															<button class="redi-range__field-button redi-range__decrement-button">

																<svg class="redi-icon">

																	<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#chevron-down"></use>

																</svg>

															</button>

														</div>

													</div>

												</div>

											</div>

										<?php endif ?>

									</div>



									<div class="redi-step-form__buttons">

										<button class="redi-button redi-button--primary redi-step-form__nav-button" disabled data-redi-step="2">

											<?php _e('Next step', 'redi-restaurant-reservation') ?>

										</button>

									</div>

								</div>



								<div class="redi-step-form__step" data-redi-step="2">

									<div class="redi-message redi-message--error redi-message--hidden" id="datepicker_error_message">

										<svg class="redi-icon">

											<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#cross"></use>

										</svg>



										<div class="text">



											<?php

											if ($waitlist) {

												if (!empty($fullyBookedMessage)) {

													echo $fullyBookedMessage;
												} else {

													_e('There are no more reservation can be made for this day.', 'redi-restaurant-reservation');
												}

												echo ('<p name="message-waitlist-form">');

												_e('In case you would like to be notified when someone cancels reservation for this day, please fill <a  href="#" class="link-waitlist-form">this form</a>.', 'redi-restaurant-reservation');

												echo ('</p>');
											} else {

												_e('There are no more reservation can be made for this day.', 'redi-restaurant-reservation');
											}

											?>



										</div>



									</div>



									<div class="redi-datepicker">

										<div class="redi-datepicker__head">

											<h5 class="redi-h5 redi-datepicker__title"><?php _e('Date', 'redi-restaurant-reservation') ?></h5>

										</div>



										<div class="redi-datepicker__body">

											<?php
											if (function_exists('icl_get_locale')) {
												$wp_locale = icl_get_locale();
											} elseif (function_exists('pll_current_language')) {
												$wp_locale = pll_current_language('locale');
											} else {
												$wp_locale = get_locale();
											}

											$wp_locale = str_replace('_', '-', $wp_locale);
											?>

											<div class="redi-calendar" form="redi_step_form" name="redi_date" data-date-format="<?php echo esc_attr(get_option('date_format')); ?>" data-locale="<?php echo esc_attr($wp_locale); ?>"></div>



											<div class="redi-datepicker__description">

												<h6 class="redi-h6 redi-datepicker__subtitle"><?php _e('Description', 'redi-restaurant-reservation') ?></h6>



												<div class="redi-datepicker__items">

													<div class="redi-datepicker__item">

														<div class="redi-datepicker__circle">8</div>

														<div class="redi-datepicker__text"><?php _e('Today', 'redi-restaurant-reservation') ?></div>

													</div>

													<div class="redi-datepicker__item redi-datepicker__item--full">

														<div class="redi-datepicker__circle">10</div>

														<div class="redi-datepicker__text"><?php _e('Reservation date', 'redi-restaurant-reservation') ?></div>

													</div>

													<div class="redi-datepicker__item redi-datepicker__item--closed">

														<div class="redi-datepicker__circle">11</div>

														<div class="redi-datepicker__text"><?php _e('Closed day or fully booked', 'redi-restaurant-reservation') ?></div>

													</div>

												</div>

											</div>

										</div>

									</div>



									<div class="redi-timepicker">

										<div class="redi-timepicker__head" style="display:none">

											<h5 class="redi-h5 redi-timepicker__title"><?php _e('Time', 'redi-restaurant-reservation') ?></h5>

										</div>



										<div class="redi-timepicker__body" style="display:none">

											<div class="redi-timepicker__nav">

												<div class="redi-timepicker__inner">

													<button id="dataRediTime" class="redi-timepicker__nav-button active" data-redi-time="all"><?php _e('All time', 'redi-restaurant-reservation') ?></button>

												</div>

											</div>

										</div>

									</div>



									<div class="redi-step-form__buttons">

										<button class="redi-button redi-button--underline redi-step-form__nav-button" data-redi-step="1"><?php _e('Back', 'redi-restaurant-reservation') ?>

										</button>

										<button class="redi-button redi-button--primary redi-step-form__nav-button" disabled data-redi-step="3"><?php _e('Next step', 'redi-restaurant-reservation') ?>

										</button>

									</div>

								</div>



								<div class="redi-step-form__step" data-redi-step="3">

									<div class="redi-message redi-message--error redi-message--hidden" id="contacts_error_message">

										<svg class="redi-icon">

											<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#cross"></use>

										</svg>

										<div class="redi-message__content">

										</div>

									</div>



									<div class="redi-contact-info">

										<h4 class="redi-h4"><?php _e('Contact information', 'redi-restaurant-reservation') ?></h4>



										<fieldset class="redi-contact-info__fields">

											<?php if (!$enablefirstlastname) : ?>


												<label for="name" class="redi-field redi-field--input redi-field--required"
													data-error-message="<?php _e('Full Name can\'t be empty', 'redi-restaurant-reservation'); ?>">

													<span class="redi-field__title"><?php _e('Full Name', 'redi-restaurant-reservation') ?></span>

													<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

													<input autocomplete="name" type="text" id="name" name="redi_name" class="redi-field__input" placeholder="<?php _e('Your Full Name', 'redi-restaurant-reservation'); ?>" required form="redi_step_form">

												</label>



											<?php else : ?>



												<label for="firstName" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('First Name can\'t be empty', 'redi-restaurant-reservation'); ?>">

													<span class="redi-field__title"><?php _e('First Name', 'redi-restaurant-reservation') ?></span>

													<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

													<input autocomplete="given-name" type="text" id="firstName" name="redi_firstName" class="redi-field__input" placeholder="<?php _e('Your First Name', 'redi-restaurant-reservation'); ?>" required form="redi_step_form">

												</label>



												<label for="nameLastname" class="redi-field redi-field--input redi-field--required"
													data-error-message="<?php _e('Last name can\'t be empty', 'redi-restaurant-reservation'); ?>">

													<span class="redi-field__title"><?php _e('Last Name', 'redi-restaurant-reservation') ?></span>

													<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

													<input type="text" autocomplete="amily-name" id="nameLastname" name="redi_nameLastname" class="redi-field__input" placeholder="<?php _e('Your Last name', 'redi-restaurant-reservation'); ?>" required form="redi_step_form">
												</label>



											<?php endif ?>



											<label for="phone" class="redi-field redi-field--input redi-field--phone redi-field--required" data-error-message="<?php _e('Incorrect phone number', 'redi-restaurant-reservation') ?>">

												<span class="redi-field__title"><?php _e('Phone', 'redi-restaurant-reservation') ?></span>

												<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

												<input type="tel" id="phone" class="redi-field__input" name="redi_phone" required autocomplete="tel" form="redi_step_form">

											</label>



											<label for="email" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Incorrect email', 'redi-restaurant-reservation') ?>">

												<span class="redi-field__title"><?php _e('Email', 'redi-restaurant-reservation') ?></span>

												<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

												<input type="email" id="email" class="redi-field__input" placeholder="your@gmail.com" required form="redi_step_form" name="redi_email">

											</label>



											<label for="comments" class="redi-field redi-field--textarea redi-start-custom_fields">

												<span class="redi-field__title"><?php _e('Comments', 'redi-restaurant-reservation') ?></span>

												<textarea id="comments" placeholder="<?php _e('Maybe you have any wishes for the reservation', 'redi-restaurant-reservation') ?>" class="redi-field__textarea" form="redi_step_form" name="redi_comments"></textarea>

											</label>





											<?php foreach ($custom_fields as $custom_field) : ?>

												<?php

												$input_field_type = 'text';

												switch ($custom_field->Type) {

													case 'options':

														$input_field_type = 'radio';

														break;

													case 'dropdown':

														$input_field_type = 'dropdown';

														break;

													case 'newsletter':

													case 'reminder':

													case 'allowsms':

													case 'checkbox':

													case 'allowwhatsapp':

													case 'gdpr':

														$input_field_type = 'checkbox';
												}

												$options = '';

												$class_required =  '';

												$error_required =  '';

												$Required = '';

												$Required_field_span = '';



												if ($custom_field->Required) :

													$class_required =  ' redi-field--required';

													$error_required =  'data-error-message="' . $custom_field->Message . '"';

													$Required = 'required';

													$Required_field_span = '<span class="redi-field__required">required field</span>';

												endif ?>

												<?php if ($input_field_type == "radio") : ?>

													<?php $field_values = explode(',', $custom_field->Values); ?>

													<?php if (!empty($custom_field->Values)) : ?>

														<fieldset class="redi-field redi-field--radios <?= $class_required ?>" <?= $error_required ?>>

															<span class="redi-field__title"><?= $custom_field->Name ?></span>

															<?= $Required_field_span ?>

															<?php foreach ($field_values as $field_value) : ?>

																<?php if ($field_value) : ?>

																	<label for="field_<?= $custom_field->Id ?>_<?= $field_value ?>" class="redi-radio">

																		<input value="<?= $field_value ?>" type="radio" name="field_<?= $custom_field->Id ?>" id="field_<?= $custom_field->Id ?>_<?= $field_value ?>" class="redi-radio__input" form="redi_step_form" <?= $Required ?>>

																		<span class="redi-radio__title"><?= $field_value ?></span>

																	</label>

																<?php endif ?>

															<?php endforeach ?>

														</fieldset>



													<?php endif ?>

												<?php endif ?>



												<?php if ($input_field_type == "dropdown") :

													$values = explode(',', $custom_field->Values);

													if (!empty($custom_field->Values)) :

														foreach ($values as $value) :

															$options .= '<option value="' . $value . '">' . $value . '</option>';

														endforeach;



												?>

														<label for="<?= $custom_field->Type ?>_<?= $custom_field->Id ?>" class="redi-field redi-field--select <?= $class_required ?>" <?= $error_required ?>>

															<span class="redi-field__title"><?= $custom_field->Name ?></span>

															<?= $Required_field_span ?>
															<!-- if select has selected option for inital - need to add 'selected' class to select tag-->
															<select name="<?= $custom_field->Type ?>_<?= $custom_field->Id ?>" id="field_<?= $custom_field->Id ?>" class="redi-field__select" form="redi_step_form" <?= $Required ?>>

																<!-- initial option for required select -->
																<option value="" selected disabled><?php _e('Select option', 'redi-restaurant-reservation'); ?></option>

																<?= $options ?>

															</select>

														</label>

													<?php endif ?>

												<?php endif ?>



												<?php if ($input_field_type == "text" || $custom_field->Type == "email") : ?>

													<label for="<?= $custom_field->Type ?>_<?= $custom_field->Id ?>" class="redi-field redi-field--input <?= $class_required ?>" <?= $error_required ?>>

														<span class='redi-field__title'><?= $custom_field->Name ?></span>

														<?= $Required_field_span ?>

														<input type="<?= $custom_field->Type ?>" id="field_<?= $custom_field->Id ?>" name="<?= $custom_field->Type ?>_<?= $custom_field->Id ?>" class="redi-field__input" form="redi_step_form" <?= $Required ?>>

													</label>

												<?php endif ?>



												<?php if ($input_field_type == "checkbox") : ?>

													<fieldset class="redi-field redi-field--checkbox <?= $class_required ?>" <?= $error_required ?>>

														<span class="redi-field__title"><?= $custom_field->Name ?></span>

														<?= $Required_field_span ?>

														<input type="<?= $custom_field->Type ?>" name="<?= $custom_field->Type ?>_<?= $custom_field->Id ?>" id="field_<?= $custom_field->Id ?>" class="redi-checkbox__input" form="redi_step_form" <?= $Required ?>>

														<span class="redi-checkbox__title"><?= $custom_field->Text ?></span>


													</fieldset>

												<?php endif ?>

											<?php endforeach; ?>

										</fieldset>

									</div>



									<div class="redi-step-form__buttons">

										<button class="redi-button redi-button--underline redi-step-form__nav-button" data-redi-step="2"><?php _e('Back', 'redi-restaurant-reservation') ?>

										</button>

										<button type="submit" class="redi-button redi-button--primary redi-step-form__nav-button" disabled data-redi-step="4"><?php _e('Make a reservation', 'redi-restaurant-reservation') ?>

										</button>

									</div>

								</div>



								<div class="redi-step-form__step" data-redi-step="4">

									<div class="redi-step-form__congrats">

										<div class="redi-step-form__checkmark">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#checkmark"></use>

											</svg>

										</div>

										<h3 class="redi-h3">
											<?php if (isset($manual) && $manual): ?>
												<?php _e('Congratulations! Your reservation request is received!', 'redi-restaurant-reservation'); ?>
											<?php else: ?>
												<?php _e('Congratulations! Your reservation is confirmed!', 'redi-restaurant-reservation'); ?>
											<?php endif; ?>
										</h3>
										<div>
											<?php if (isset($manual) && $manual): ?>
												<?php _e('We have received your reservation request and are checking availability. You will be notified as soon as your reservation is approved. Please wait for the confirmation.', 'redi-restaurant-reservation'); ?>
											<?php else: ?>
												<?php _e('A confirmation email has been sent to your provided email address. No further action is required. If you don\'t receive it, rest assured your reservation is confirmed. You can also contact us by phone if needed.', 'redi-restaurant-reservation'); ?>
											<?php endif; ?>
										</div>
										<p>
											<?php if (isset($manual) && $manual): ?>
												<?php _e('Your reservation request number for reference:', 'redi-restaurant-reservation'); ?>
											<?php else: ?>
												<?php _e('Your reservation number for reference:', 'redi-restaurant-reservation'); ?>
											<?php endif; ?>

										</p>
										<p>
											<span id="reservation-id" style="font-weight: bold"></span>
										</p>


									</div>

									<?php if ($userfeedback == 'true') : ?>

										<form class="redi-rate-form" action="#">

											<label for="redi_rate_comments" class="redi-field redi-field--rate">

												<span class="redi-field__title"><?php _e('Rate your reservation experience', 'redi-restaurant-reservation') ?></span>



												<div class="redi-rating redi-rating-to-set" data-value="5">

													<div class="redi-rating__body">

														<div class="redi-rating__active" style="width: 100%;"></div>

														<div class="redi-rating__items">

															<input type="radio" class="redi-rating__item" value="1" name="redi_rating">

															<input type="radio" class="redi-rating__item" value="2" name="redi_rating">

															<input type="radio" class="redi-rating__item" value="3" name="redi_rating">

															<input type="radio" class="redi-rating__item" value="4" name="redi_rating">

															<input type="radio" class="redi-rating__item" value="5" name="redi_rating">

														</div>

													</div>

												</div>

											</label>



											<label for="redi_rate_comments" class="redi-field redi-field--textarea">

												<span class="redi-field__title"><?php _e('What we can do better?', 'redi-restaurant-reservation') ?></span>

												<textarea id="redi_rate_comments" name="redi_rate_comments" class="redi-field__textarea"></textarea>

											</label>



											<button type="submit" class="redi-button redi-button--primary">Send</button>

										</form>

									<?php endif; ?>

								</div>

								<?php if ($captcha) : ?>

									<div class="redi-step-block-captcha">

										<script src="https://www.google.com/recaptcha/api.js" async defer></script>

										<div id="redi-captcha" class="g-recaptcha" data-sitekey="<?php echo $captchaKey ?>"></div>

									</div>

								<?php endif ?>

								<form id="redi_step_form"></form>

							</div>

						</div>



						<aside class="redi-step-form__sidebar">

							<div class="redi-step-form__links">

								<a href="#reservation_details_popup" class="redi-link redi-popup__button">

									<span><?php _e('View Reservation Details', 'redi-restaurant-reservation') ?></span>

									<svg class="redi-icon">

										<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#eye"></use>

									</svg>

								</a>

								<?php if (isset($EnableModifyReservations) && $EnableModifyReservations) : ?>

									<!-- Modify existing reservation -->

									<button class="redi-link redi-route__button" data-redi-route="modify_reservation">

										<span><?php _e('Modify existing reservation', 'redi-restaurant-reservation') ?></span>

										<svg class="redi-icon">

											<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#edit"></use>

										</svg>

									</button>

								<?php endif ?>



								<?php if (isset($EnableCancelForm) && $EnableCancelForm) : ?>

									<!-- Cancel existing reservation -->

									<button class="redi-link redi-route__button" data-redi-route="cancel_reservation">

										<span><?php _e('Cancel existing reservation', 'redi-restaurant-reservation') ?></span>

										<svg class="redi-icon">

											<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#attention"></use>

										</svg>

									</button>

								<?php endif ?>



							</div>



							<div id="reservation_details_popup" class="redi-reservation-details redi-popup">

								<button class="redi-reservation-details__cross-button redi-popup__close-button">

									<svg class="redi-icon">

										<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#cross"></use>

									</svg>

								</button>



								<h5 class="redi-h5 redi-reservation-details__title"><?php _e('Reservation Details', 'redi-restaurant-reservation') ?></h5>



								<div class="redi-view-reservation">

									<div class="redi-view-reservation__order-info">

										<div class="redi-view-reservation__item redi-view-reservation__item--place">

											<div class="redi-view-reservation__place-name">

												<svg class="redi-icon">

													<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#pin"></use>

												</svg>

												<span class="redi-empty" data-default-text-fake="Empty">-</span>

												<span data-default-text="Empty" id="redi_place" style="color:transparent;"><?php echo $places[0]->Name; ?></span>

											</div>

											<address class="redi-view-reservation__item redi-view-reservation__item--address" style="color:transparent;" id="redi_place_address"><?php echo $places[0]->Address; ?></address>

										</div>



										<div class="redi-view-reservation__item redi-view-reservation__item--guests-qty">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#people"></use>

											</svg>



											<span data-default-text="Empty" id="redi_persons"><?php echo $minPersons; ?> <?php _e('Guests', 'redi-restaurant-reservation'); ?></span>

										</div>

										<?php if ($childrenSelection) : ?>



											<div class="redi-view-reservation__item redi-view-reservation__item--childs-qty">

												<svg class="redi-icon">

													<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#child"></use>

												</svg>

												<span class="redi-empty" data-default-text="Empty" id="redi_children">0</span>

											</div>

										<?php endif ?>

										<div class="redi-view-reservation__item redi-view-reservation__item--date">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#calendar"></use>

											</svg>


											<span class="redi-empty" data-default-text="<?php esc_attr_e('Empty', 'redi-restaurant-reservation'); ?>"
												id="redi_date">-</span>


										</div>



										<div class="redi-view-reservation__item redi-view-reservation__item--time">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#watch"></use>

											</svg>

											<span class="redi-empty" data-default-text="Empty" id="redi_time">-</span>

										</div>

									</div>



									<div class="redi-view-reservation__contact-info">

										<h5 class="redi-h5 redi-view-reservation__title">Contact Person</h5>



										<div class="redi-view-reservation__item redi-view-reservation__item--person">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#person"></use>

											</svg>

											<span class="redi-empty" data-default-text="Empty" id="redi_name"><?php _e('Empty', 'redi-restaurant-reservation') ?></span>

										</div>



										<div class="redi-view-reservation__item redi-view-reservation__item--phone">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#phone"></use>

											</svg>

											<span class="redi-empty" data-default-text="Empty" id="redi_phone"><?php _e('Empty', 'redi-restaurant-reservation') ?></span>

										</div>



										<div class="redi-view-reservation__item redi-view-reservation__item--email">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#email"></use>

											</svg>

											<span class="redi-empty" data-default-text="Empty" id="redi_email"><?php _e('Empty', 'redi-restaurant-reservation') ?></span>

										</div>



										<div class="redi-view-reservation__item redi-view-reservation__item--comments">

											<svg class="redi-icon">

												<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#comments"></use>

											</svg>

											<div class="redi-empty" data-default-text="Empty" id="redi_comments"><?php _e('Empty', 'redi-restaurant-reservation') ?>

											</div>

										</div>

									</div>

								</div>



								<button class="redi-button redi-button--primary redi-popup__close-button"><?php _e('Back', 'redi-restaurant-reservation') ?></button>

							</div>

						</aside>

					</div>

				</div>



				<?php if (isset($EnableModifyReservations) && $EnableModifyReservations) : ?>

					<!-- Modify existing reservation -->

					<div class="redi-layout__route redi-route redi-modify-reservation" data-redi-route="modify_reservation">

						<div class="redi-layout__head">

							<h2 class="redi-h2 redi-layout__title"><?php _e('Modify reservation', 'redi-restaurant-reservation') ?></h2>

						</div>

						<div class="redi-layout__body">

							<div class="redi-layout__main">

								<form class="redi-modify-reservation__form">

									<div class="redi-message redi-message--error redi-message--hidden" id="modify_error_message">

										<svg class="redi-icon">

											<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#cross"></use>

										</svg>

										<div class="redi-message__content">

										</div>

									</div>



									<label for="modify_order_number" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Reservation number can\'t be empty', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Reservation number', 'redi-restaurant-reservation') ?>:</span>

										<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

										<input type="text" id="modify_order_number" class="redi-field__input" required name="modify_order_number">

									</label>



									<label for="redi_modify_phone" class="redi-field redi-field--input redi-field--phone redi-field--required1" data-error-message="<?php _e('Incorrect phone number', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Phone', 'redi-restaurant-reservation') ?>:</span>

										<!-- <span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span> -->

										<input type="text" id="redi_modify_phone" class="redi-field__input" name="redi_modify_phone" autocomplete="new-password" form="redi_step_form">

									</label>



									<label for="redi_modify_name" class="redi-field redi-field--input redi-field--required1 <?= is_user_logged_in() ? 'has-value' : ''; ?>" data-error-message="<?php _e('Name can\'t be empty', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Name', 'redi-restaurant-reservation') ?>:</span>

										<!-- <span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span> -->

										<input <?= is_user_logged_in() ? 'value="' . wp_get_current_user()->display_name . '"' : ''; ?> type="text" id="redi_modify_name" name="redi_modify_name" class="redi-field__input" placeholder="<?php _e('Your name', 'redi-restaurant-reservation'); ?>" form="redi_step_form">

									</label>



									<label for="redi_modify_email" class="redi-field redi-field--input redi-field--required1 <?= is_user_logged_in() ? 'has-value' : ''; ?>" data-error-message="<?php _e('Incorrect email', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Email', 'redi-restaurant-reservation') ?>:</span>

										<!-- <span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span> -->

										<input <?= is_user_logged_in() ? 'value="' . wp_get_current_user()->user_email . '"' : ''; ?> type="email" id="redi_modify_email" class="redi-field__input" placeholder="your@gmail.com" form="redi_step_form" name="redi_modify_email">

									</label>







									<div class="redi-modify-reservation__buttons">

										<button id="redi-restaurant-modify" type="submit" class="redi-button redi-button--primary redi-route__button" data-redi-route="update_reservation">

											<?php _e('Find reservation', 'redi-restaurant-reservation') ?>

										</button>

									</div>

								</form>

							</div>



							<aside class="redi-layout__sidebar">

								<button id="redi-restaurant-find" class="redi-button redi-button--icon redi-route__button" data-redi-route="redi_step_form">

									<svg class="redi-icon">

										<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#bookmark-border"></use>

									</svg>

									<span><?php _e('Create reservation', 'redi-restaurant-reservation') ?></span>

								</button>

							</aside>

						</div>

					</div>

					<!-- update form -->

					<div class="redi-layout__route redi-route redi-update-reservation" data-redi-route="update_reservation">

						<div class="redi-layout__head">

							<h2 class="redi-h2 redi-layout__title"><?php _e('Update reservation', 'redi-restaurant-reservation') ?></h2>

						</div>

						<div class="redi-layout__body">

							<div class="redi-layout__main">

								<form class="redi-update-reservation__form">

									<div class="redi-message redi-message--error redi-message--hidden" id="update_error_message">

										<svg class="redi-icon">

											<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#cross"></use>

										</svg>

										<div class="redi-message__content">

										</div>

									</div>

									<p><?php _e('Date', 'redi-restaurant-reservation') ?>: <span class="redi-update-dateN" id="updateDateFromN"></span></p>

									<p><?php _e('Time', 'redi-restaurant-reservation') ?>: <span class="redi-update-timeN" id="updateTimeFromN"></span></p>

									<div class="redi-ranges">

										<!-- [data-redi-max] is optional attribute -->

										<div class="redi-range redi-range--persons" data-redi-max="<?php echo $maxPersons; ?>" data-redi-min="<?php echo $minPersons; ?>">

											<div class="redi-range__head">

												<h5 class="redi-h5 redi-range__title"><?php _e('Number of guests', 'redi-restaurant-reservation') ?>:</h5>

											</div>

											<div class="redi-range__body">

												<div class="redi-range__slider">

													<div class="redi-range__labels">

														<div class="redi-range__label"><?php echo $minPersons; ?></div>

														<div class="redi-range__label"><?php echo $maxPersons; ?></div>

													</div>



													<div class="redi-range__inner">

														<div class="redi-range__line">

															<div id="redi_update_progress" class="redi-range__progress"></div>

															<input id="persons_update_slider" type="range" class="redi-range__range-input" min="<?php echo $minPersons; ?>" max="<?php echo $maxPersons; ?>" value="0" data-value="<?php echo $minPersons; ?>" form="redi_step_form" name="redi_persons">

														</div>

													</div>

												</div>



												<span class="redi-separator"><?php _e('OR', 'redi-restaurant-reservation') ?></span>



												<div class="redi-range__field">

													<input id="persons_update" type="text" class="redi-range__input" value="">

													<div class="redi-range__buttons">

														<button class="redi-range__field-button redi-range__increment-button">

															<svg class="redi-icon">

																<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#chevron-up"></use>

															</svg>

														</button>

														<button class="redi-range__field-button redi-range__decrement-button">

															<svg class="redi-icon">

																<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#chevron-down"></use>

															</svg>

														</button>

													</div>

												</div>

											</div>

										</div>

									</div>





									<label for="redi_update_name" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Name can\'t be empty', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Name', 'redi-restaurant-reservation') ?></span>

										<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

										<input type="text" id="redi_update_name" name="redi_update_name" class="redi-field__input" placeholder="<?php _e('Your name', 'redi-restaurant-reservation'); ?>" required form="redi_step_form">

									</label>



									<label for="redi_update_phone" class="redi-field redi-field--input redi-field--phone redi-field--required" data-error-message="<?php _e('Incorrect phone number', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Phone', 'redi-restaurant-reservation') ?></span>

										<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

										<input type="text" id="redi_update_phone" class="redi-field__input" name="redi_update_phone" required autocomplete="new-password" form="redi_step_form">

									</label>





									<label for="redi_update_email" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Incorrect email', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Email', 'redi-restaurant-reservation') ?></span>

										<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

										<input type="email" id="redi_update_email" class="redi-field__input" placeholder="<?php _e('your@email.com', 'redi-restaurant-reservation'); ?>" required form="redi_step_form" name="redi_update_email">

									</label>



									<label for="redi_update_comments" class="redi-field redi-field--textarea" data-error-message="<?php _e('Comments can\'t be empty', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Comments', 'redi-restaurant-reservation') ?></span>

										<!-- <span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span> -->

										<textarea id="redi_update_comments" class="redi-field__textarea" name="redi_update_comments"></textarea>

									</label>





									<input type="hidden" value="" name="redi-restaurant-updateID" id="redi-restaurant-updateIDv2">

									<input type="hidden" value="" name="updatePlaceReferenceId" id="updatePlaceReferenceIdv2">

									<input type="hidden" value="" name="updateTo" id="updateTov2">

									<input type="hidden" value="" name="updateFrom" id="updateFromv2">



									<div class="redi-update-reservation__buttons">

										<button id="redi-restaurant-update" type="submit" class="redi-button redi-button--primary redi-route__button button-update" data-redi-route="reservation_is_cancel">

											<?php _e('Update reservation', 'redi-restaurant-reservation') ?>

										</button>

									</div>

								</form>

							</div>



							<aside class="redi-layout__sidebar">

								<button id="redi-restaurant-find" class="redi-button redi-button--icon redi-route__button" data-redi-route="redi_step_form">

									<svg class="redi-icon">

										<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#bookmark-border"></use>

									</svg>

									<span><?php _e('Create reservation', 'redi-restaurant-reservation') ?></span>

								</button>

							</aside>

						</div>

					</div>





				<?php endif ?>



				<?php if (isset($EnableCancelForm) && $EnableCancelForm) : ?>

					<!-- Cancel existing reservation -->

					<div class="redi-layout__route redi-route redi-cancel-reservation" data-redi-route="cancel_reservation">

						<div class="redi-layout__head">

							<h2 class="redi-h2 redi-layout__title"><?php _e('Cancel reservation', 'redi-restaurant-reservation') ?></h2>

						</div>



						<div class="redi-layout__body">

							<div class="redi-layout__main">

								<form class="redi-cancel-reservation__form">

									<div class="redi-message redi-message--error redi-message--hidden" id="cancel_error_message">

										<svg class="redi-icon">

											<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#cross"></use>

										</svg>

										<div class="redi-message__content">

										</div>

									</div>



									<label for="cancel_order_number" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Reservation number can\'t be empty', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Reservation number', 'redi-restaurant-reservation') ?></span>

										<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

										<input type="text" id="cancel_order_number" class="redi-field__input" required name="cancel_order_number">

									</label>



									<label for="redi_cancel_phone" class="redi-field redi-field--input redi-field--phone " data-error-message="<?php _e('Incorrect phone number', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Phone', 'redi-restaurant-reservation') ?></span>

										<input type="text" id="redi_cancel_phone" class="redi-field__input" name="redi_cancel_phone" autocomplete="new-password" form="redi_step_form">

									</label>





									<label for="redi_cancel_name" class="redi-field redi-field--input " data-error-message="<?php _e('Name can\'t be empty', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Name', 'redi-restaurant-reservation') ?></span>

										<input <?= is_user_logged_in() ? 'value="' . wp_get_current_user()->display_name . '"' : ''; ?> type="text" id="redi_cancel_name" name="redi_cancel_name" class="redi-field__input" placeholder="<?php _e('Your name', 'redi-restaurant-reservation'); ?>" form="redi_step_form">

									</label>



									<label for="redi_cancel_email" class="redi-field redi-field--input " data-error-message="<?php _e('Incorrect email', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Email', 'redi-restaurant-reservation') ?></span>

										<input <?= is_user_logged_in() ? 'value="' . wp_get_current_user()->user_email . '"' : ''; ?> type="email" id="redi_cancel_email" class="redi-field__input" placeholder="<?php _e('your@email.com', 'redi-restaurant-reservation'); ?>" form="redi_step_form" name="redi_cancel_email">

									</label>

									<label for="redi_cancel_reason" class="redi-field redi-field--textarea
									<?php if ($mandatoryCancellationReason) echo 'redi-field--required'; ?>
									" data-error-message="<?php _e('Reason can\'t be empty', 'redi-restaurant-reservation') ?>">

										<span class="redi-field__title"><?php _e('Reason', 'redi-restaurant-reservation') ?></span>

										<?php if ($mandatoryCancellationReason) : ?>
											<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>
										<?php endif; ?>

										<textarea id="redi_cancel_reason" class="redi-field__textarea" name="redi_cancel_reason" <?php if ($mandatoryCancellationReason) echo 'required'; ?>></textarea>

									</label>

									<?php
									// endif
									?>



									<div class="redi-cancel-reservation__buttons">

										<button id="redi-restaurant-cancel" type="submit" class="redi-button redi-button--primary redi-route__button button-cancel" data-redi-route="reservation_is_cancel">

											<?php _e('Cancel reservation', 'redi-restaurant-reservation') ?>

										</button>

										<!-- <button type="button" class="redi-button redi-button--underline redi-route__button" data-redi-route="auth">

                                            <?php // _e('Cancel', 'redi-restaurant-reservation') 
																						?>

                                        </button> -->

									</div>

								</form>

							</div>



							<aside class="redi-layout__sidebar">

								<button id="redi-restaurant-find" class="redi-button redi-button--icon redi-route__button" data-redi-route="redi_step_form">

									<svg class="redi-icon">

										<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#bookmark-border"></use>

									</svg>

									<span><?php _e('Create reservation', 'redi-restaurant-reservation') ?></span>

								</button>

							</aside>

						</div>

					</div>

				<?php endif ?>





				<!-- white list -->



				<div class="redi-layout__route redi-route redi-white_list-reservation" data-redi-route="white_list_reservation">

					<div class="redi-layout__head">

						<h2 class="redi-h2 redi-layout__title"><?php _e('Wait List', 'redi-restaurant-reservation') ?></h2>

					</div>



					<div class="redi-layout__body">

						<div class="redi-layout__main">

							<form class="redi-white_list-reservation__form">

								<div class="redi-message redi-message--error redi-message--hidden" id="white_list_error_message">

									<svg class="redi-icon">

										<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#cross"></use>

									</svg>

									<div class="redi-message__content">

									</div>

								</div>



								<p><?php _e('Date', 'redi-restaurant-reservation') ?>: <span class="redi-update-dateN" id="white_listDateFrom"></span></p>

								<p><?php _e('Persons', 'redi-restaurant-reservation') ?>: <span class="redi-update-timeN" id="white_listPersonFrom"></span></p>





								<label for="white_list_preferred_time" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Reservation number can\'t be empty', 'redi-restaurant-reservation') ?>">

									<span class="redi-field__title"><?php _e('Preferred time', 'redi-restaurant-reservation') ?></span>

									<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

									<input type="time" id="white_list_preferred_time" class="redi-field__input" required name="white_list_preferred_time">

								</label>



								<label for="redi_white_list_name" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Name can\'t be empty', 'redi-restaurant-reservation') ?>">

									<span class="redi-field__title"><?php _e('Name', 'redi-restaurant-reservation') ?></span>

									<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

									<input <?= is_user_logged_in() ? 'value="' . wp_get_current_user()->display_name . '"' : ''; ?> type="text" id="redi_white_list_name" name="redi_white_list_name" class="redi-field__input" placeholder="<?php _e('Your name', 'redi-restaurant-reservation'); ?>" required form="redi_step_form">

								</label>



								<label for="redi_white_list_phone" class="redi-field redi-field--input redi-field--phone redi-field--required" data-error-message="<?php _e('Incorrect phone number', 'redi-restaurant-reservation') ?>">

									<span class="redi-field__title"><?php _e('Phone', 'redi-restaurant-reservation') ?></span>

									<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

									<input type="text" id="redi_white_list_phone" class="redi-field__input" name="redi_white_list_phone" required autocomplete="new-password" form="redi_step_form">

								</label>



								<label for="redi_white_list_email" class="redi-field redi-field--input redi-field--required" data-error-message="<?php _e('Incorrect email', 'redi-restaurant-reservation') ?>">

									<span class="redi-field__title"><?php _e('Email', 'redi-restaurant-reservation') ?></span>

									<span class="redi-field__required"><?php _e('required field', 'redi-restaurant-reservation') ?></span>

									<input <?= is_user_logged_in() ? 'value="' . wp_get_current_user()->user_email . '"' : ''; ?> type="email" id="redi_white_list_email" class="redi-field__input" placeholder="<?php _e('your@email.com', 'redi-restaurant-reservation'); ?>" required form="redi_step_form" name="redi_white_list_email">

								</label>



								<div class="redi-white_list-reservation__buttons">

									<button id="redi-restaurant-white_list" type="submit" class="redi-button redi-button--primary redi-route__button button-white_list" data-redi-route="reservation_is_cancel">

										<?php _e('Register to Wait List', 'redi-restaurant-reservation') ?>

									</button>

								</div>

							</form>

						</div>



						<aside class="redi-layout__sidebar">

							<button id="redi-restaurant-find" class="redi-button redi-button--icon redi-route__button" data-redi-route="redi_step_form">

								<svg class="redi-icon">

									<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#bookmark-border"></use>

								</svg>

								<span><?php _e('Create reservation', 'redi-restaurant-reservation') ?></span>

							</button>

						</aside>

					</div>

				</div>



				<!-- Okay CANCEL! -->

				<div class="redi-layout__route redi-route redi-cancel-reservation redi-cancel-reservation--end" data-redi-route="reservation_is_cancel">

					<div class="redi-layout__body">

						<div class="redi-layout__main">


							<div id="order-cancel" style="display:none">
								<h3 class="redi-h3">
									<?php _e('Your reservation is cancelled successfully!', 'redi-restaurant-reservation'); ?>
								</h3>
								<p class="redi-paragraph">
									<?php _e('Thank you for letting us know about your cancellation - this helps other guests find a table! We hope to see you back soon. Would you like to make another reservation for a different date?', 'redi-restaurant-reservation') ?>
								</p>
							</div>

							<h3 class="redi-h3" id="order-update" style="display:none">

								<?php _e('Your reservation has been updated successfully!', 'redi-restaurant-reservation'); ?>
							</h3>

							<div id="wait-list-success" style="display: none;" class="redi-reservation-alert-success redi-reservation-alert">

								<h3 class="redi-h3">
									<?php _e('Your request has been received!', 'redi-restaurant-reservation'); ?>
								</h3>

								<p class="redi-paragraph">
									<?php _e('Your information has been saved successfully and you will be notified once there are available seats for requested date.', 'redi-restaurant-reservation') ?>
								</p>
							</div>


						</div>



						<aside class="redi-layout__sidebar">

							<button id="redi-restaurant-find" class="redi-button redi-button--icon redi-route__button" data-redi-route="redi_step_form">

								<svg class="redi-icon">

									<use xlink:href="<?php echo site_url(); ?>/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#bookmark-border"></use>

								</svg>

								<span><?php _e('Create reservation', 'redi-restaurant-reservation') ?></span>

							</button>

						</aside>

					</div>

				</div>

			</div>

		</div>

	</section>

</div>





<?php $default_duration = $default_reservation_duration; ?>

<input id="redi-restaurant-startDateISO" type="hidden" value="<?php echo $startDateISO ?>" name="startDateISO" />

<input type="hidden" id="duration" value="<?php echo $default_duration ?>" />

<input id="redi-restaurant-startTime-alt" type="hidden" value="<?php echo date_i18n('H:i', $startTime); ?>" name="startTime" />