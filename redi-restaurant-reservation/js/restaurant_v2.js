const DIR_URL = window.location.protocol + '//' + window.location.host;
let stepFormState = {
  redi_place: null,
  redi_persons: min_persons,
  redi_children: null,
  redi_date: null,
  redi_time: null,
  redi_name: null,
  redi_LastName: null,
  redi_phone: null,
  redi_email: null,
  redi_comments: null,
  redi_titleButton: document.getElementById('dataRediTime').innerHTML,
  redi_fonecode: null,
};
window.validation = {
  containers: null,
  errorMessage: null,
  nextButton: null,
  key: {},
};

let acf = [];

const setParentContainerClass = () => {
  const app = document.getElementsByClassName('redi-app')[0];

  if (app === undefined) return;

  const parent = app.parentElement;
  parent.classList.add('has-redi-app');
};

// throttle with delay function invoke

const throttled = (delay, fn) => {
  let lastCall = 0;

  return (...args) => {
    const now = new Date().getTime();
    if (now - lastCall < delay) return;

    lastCall = now;
    return fn(...args);
  };
};

// get elements position from edges
const getElementPositionFromEdges = (element) => {
  const rect = element.getBoundingClientRect();
  const windowWidth = window.innerWidth;
  const windowHeight = window.innerHeight;

  return {
    top: rect.top,
    bottom: windowHeight - rect.bottom,
    left: rect.left,
    right: windowWidth - rect.right,
  };
};

// find number that less then 10 and bigger than 0 or negative number in object
const findSpecialNumber = (obj) => {
  let result = null;

  for (let key in obj) {
    if (
      obj.hasOwnProperty(key) &&
      typeof obj[key] === 'number' &&
      (obj[key] < 0 || obj[key] < 10)
    ) {
      result = {
        key: key,
        value: obj[key],
      };
    }
  }

  return result;
};

// format date
const formatDate = (dateString, format = null, locale = 'en-US') => {
  const date = new Date(dateString);
  if (isNaN(date)) return 'Invalid date';

  const day = date.getDate().toString().padStart(2, '0');
  const month = (date.getMonth() + 1).toString().padStart(2, '0');
  const year = date.getFullYear();

  if (!format) {
    return new Intl.DateTimeFormat(locale, {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    }).format(date);
  }

  return format
    .replace('d', day)
    .replace('m', month)
    .replace('Y', year)
    .replace('j', date.getDate())
    .replace('n', date.getMonth() + 1)
    .replace('F', date.toLocaleString(locale, { month: 'long' }))
    .replace('M', date.toLocaleString(locale, { month: 'short' }));
};

// hadle tabs

const removeSpaces = (str) => {
  return str.replace(/\s+/g, '');
};

const removeStartString = (str1, str2) => {
  if (str1.startsWith(str2)) {
    return str1;
  }
  return str2 + str1;
};

const handleTabs = (buttonClassName, tabClassName, dataName) => {
  const buttons = document.getElementsByClassName(buttonClassName);
  const tabs = document.getElementsByClassName(tabClassName);

  if (!buttons.length || !tabs.length) return;

  const closeAll = () => {
    [...buttons].forEach((button) => button.classList.remove('active'));
    [...tabs].forEach((tab) => tab.classList.remove('active'));
    document.querySelector('#order-cancel').style.display = 'none';
    document.querySelector('#order-update').style.display = 'none';
    document.querySelector('#wait-list-success').style.display = 'none';
  };

  [...buttons].forEach((button) => {
    button.addEventListener('click', (event) => {
      if (button && button.id === 'redi-restaurant-cancel') {
        event.preventDefault();
        window.handleFormFields(
          '.redi-cancel-reservation__form .redi-field--required',
          '#cancel_error_message',
          '.redi-route__button.button-cancel[data-redi-route="reservation_is_cancel"]'
        );

        let hasTrueValue = false;

        window.validation.containers.forEach((container) => {
          const field = container.querySelector(
            'input:not([type=radio]):not([type=checkbox]):not(.iti__search-input), textarea, select'
          );

          if (field === null) return;

          field.dispatchEvent(new Event('blur'));
          hasTrueValue = Object.values(window.validation.key).some(
            (value) => value === true
          );
        });

        if (hasTrueValue) return;

        reservationCancel(function (response) {
          if (response['Error']) {
            cancelOrderMessageError(response.Error, 'cancel_error_message');
          } else {
            closeAll();
            document.querySelector('#order-cancel').style.display = 'block';
            const dataValue = button.getAttribute(dataName);
            [...tabs].forEach((tab) => {
              if (dataValue === tab.getAttribute(dataName)) {
                button.classList.add('active');
                tab.classList.add('active');
              }
            });
          }
        });
      } else if (button && button.id === 'redi-restaurant-modify') {
        event.preventDefault();

        window.handleFormFields(
          '.redi-modify-reservation__form .redi-field--required',
          '#modify_error_message',
          '.redi-route__button[data-redi-route="update_reservation"]'
        );

        const errorMessage = document.querySelector('#update_error_message');

        if (errorMessage !== null) {
          errorMessage.classList.add('redi-message--hidden');
        }

        let hasTrueValue = false;

        window.validation.containers.forEach((container) => {
          const field = container.querySelector(
            'input:not([type=radio]):not([type=checkbox]):not(.iti__search-input), textarea, select'
          );

          if (field === null) return;

          field.dispatchEvent(new Event('blur'));
          hasTrueValue = Object.values(window.validation.key).some(
            (value) => value === true
          );
        });

        if (hasTrueValue) return;

        reservationModifi(function (response) {
          if (response.reservation['Error']) {
            cancelOrderMessageError(
              response.reservation.Error,
              'modify_error_message'
            );
          } else {
            addModifiForm(response);
            closeAll();
            const dataValue = button.getAttribute(dataName);
            [...tabs].forEach((tab) => {
              if (dataValue === tab.getAttribute(dataName)) {
                button.classList.add('active');
                tab.classList.add('active');
              }
            });
          }
        });
      } else if (button && button.id === 'redi-restaurant-update') {
        event.preventDefault();
        window.handleFormFields(
          '.redi-update-reservation__form .redi-field--required',
          '#update_error_message',
          '.button-update.redi-route__button[data-redi-route="reservation_is_cancel"]'
        );

        let hasTrueValue = false;

        window.validation.containers.forEach((container) => {
          const field = container.querySelector(
            'input:not([type=radio]):not([type=checkbox]):not(.iti__search-input), textarea, select'
          );

          if (field === null) return;

          field.dispatchEvent(new Event('blur'));
          hasTrueValue = Object.values(window.validation.key).some(
            (value) => value === true
          );
        });

        if (hasTrueValue) return;

        reservationUpdate(function (response) {
          const messageContainer = document.querySelector(
            '#update_error_message'
          );

          if (response.Error) {
            if (messageContainer !== null) {
              const contentContainer = messageContainer.querySelector(
                '.redi-message__content'
              );

              if (contentContainer !== null) {
                contentContainer.textContent = response.Error;
                messageContainer.classList.remove('redi-message--hidden');
              }
            }

            return;
          } else {
            messageContainer.classList.add('redi-message--hidden');
          }

          closeAll();
          document.querySelector('#order-update').style.display = 'block';
          const dataValue = button.getAttribute(dataName);
          [...tabs].forEach((tab) => {
            if (dataValue === tab.getAttribute(dataName)) {
              button.classList.add('active');
              tab.classList.add('active');
            }
          });
        });
      } else if (button && button.id === 'redi-restaurant-white_list') {
        event.preventDefault();

        window.handleFormFields(
          '.redi-white_list-reservation__form .redi-field--required',
          '#white_list_error_message',
          '.button-white_list.redi-route__button[data-redi-route="reservation_is_cancel"]'
        );

        let hasTrueValue = false;

        window.validation.containers.forEach((container) => {
          const field = container.querySelector(
            'input:not([type=radio]):not([type=checkbox]):not(.iti__search-input), textarea, select'
          );

          if (field === null) return;

          field.dispatchEvent(new Event('blur'));
          hasTrueValue = Object.values(window.validation.key).some(
            (value) => value === true
          );
        });

        if (hasTrueValue) return;

        whiteListSend(function (response) {
          if (response.Error) {
            cancelOrderMessageError(response.Error, 'white_list_error_message');
          } else {
            closeAll();
            document.querySelector('#wait-list-success').style.display =
              'block';

            const dataValue = button.getAttribute(dataName);
            [...tabs].forEach((tab) => {
              if (dataValue === tab.getAttribute(dataName)) {
                button.classList.add('active');
                tab.classList.add('active');
              }
            });
          }
        });
      } else {
        event.preventDefault();
        closeAll();
        const dataValue = button.getAttribute(dataName);
        // const dataValue = button.dataset.dataName;

        [...tabs].forEach((tab) => {
          if (
            button &&
            button.id === 'redi-restaurant-find' &&
            dataValue === tab.getAttribute(dataName)
          ) {
            button.classList.add('active');
            tab.classList.add('active');
            setActiveTab();
          } else {
            if (dataValue === tab.getAttribute(dataName)) {
              button.classList.add('active');
              tab.classList.add('active');
            }
          }
        });
      }
    });
  });
};

const setActiveTab = () => {
  const steps = document.getElementsByClassName('redi-step-form__step');
  const step = document.querySelector(
    '.redi-step-form__step[data-redi-step="1"]'
  );

  const date = document.getElementById('redi_date');
  const time = document.getElementById('redi_time');

  const indicatorsContainer = document.getElementsByClassName(
    'redi-steps-indicator'
  )[0];
  const indicators = document.getElementsByClassName(
    'redi-steps-indicator__step'
  );

  const removeAllActiveClasses = () => {
    [...steps].forEach((route) => route.classList.remove('active'));
  };

  const setActiveIndicator = (currentStep) => {
    [...indicators].forEach((indicator) =>
      indicator.classList.remove('active')
    );

    [...indicators].forEach((indicator) => {
      const step = indicator.getAttribute('data-redi-step');
      if (step === currentStep) {
        indicator.classList.add('active');
        indicatorsContainer.setAttribute('data-redi-current-step', step);
      }
    });
  };
  removeAllActiveClasses();
  setActiveIndicator('1');

  step.classList.add('active');
  // date.classList.add('redi-empty')
  // date.innerText = '-'
  // time.classList.add('redi-empty')
  // time.innerText = '-'
  // qeryDataCalendarGetWork();
};

const cancelOrderMessageError = (errorText, idContainerErrorMessenge) => {
  errorMessageContainer = document.getElementById(idContainerErrorMessenge);
  if (
    errorMessageContainer !== null &&
    errorMessageContainer.classList.contains('redi-message--hidden')
  ) {
    errorMessageContainer.querySelector('.redi-message__content').innerHTML =
      errorText;
    errorMessageContainer.classList.remove('redi-message--hidden');
  }
};

const addModifiForm = (data) => {
  let range = document.getElementById('redi_update_progress');
  range.style.width =
    ((data.reservation.Persons - min_persons) / (max_persons - min_persons)) *
      100 +
    '%';

  jQuery('#persons_update').val(data.reservation.Persons);
  jQuery('#persons_update_slider').val(data.reservation.Persons);
  jQuery('#updateDateFromN').text(data.startDate);
  jQuery('#updateTimeFromN').text(data.startTime);
  jQuery('#redi_update_name').val(data.reservation.Name);
  jQuery('#redi_update_phone').val(data.reservation.Phone);
  const phoneNumber = data.reservation.Phone;
  const formContainer = document.querySelector(
    '.redi-update-reservation__form'
  );

  if (formContainer) {
    const selectedCountryButton = formContainer.querySelector(
      '.iti__selected-country'
    );
    if (selectedCountryButton) {
      setTimeout(() => {
        selectedCountryButton.click();
        const countryListItems = formContainer.querySelectorAll(
          '#iti-2__country-listbox .iti__country'
        );
        let matchedItemId = null;
        let countryCode = '';
        countryListItems.forEach((li) => {
          const dialCodeSpan = li.querySelector('.iti__dial-code');
          if (
            dialCodeSpan &&
            phoneNumber.startsWith(dialCodeSpan.textContent.trim())
          ) {
            matchedItemId = li.id;
            countryCode = dialCodeSpan.textContent.trim();
          }
        });
        if (matchedItemId) {
          const matchedItem = document.getElementById(matchedItemId);
          if (matchedItem) {
            matchedItem.click();
            const numberWithoutCountryCode = phoneNumber
              .replace(countryCode, '')
              .replace('+', '')
              .trim();

            jQuery('#redi_update_phone').val(numberWithoutCountryCode);
          }
        }
      }, 50);
    }
  }

  jQuery('#redi_update_email').val(data.reservation.Email);
  // jQuery('#redi_update_comments').val(data.reservation.Comments);
  jQuery('#redi-restaurant-updateIDv2').val(
    jQuery('#modify_order_number').val()
  );
  jQuery('#updatePlaceReferenceIdv2').val(data.reservation.PlaceReferenceId);
  jQuery('#updateFromv2').val(data.reservation.From);
  jQuery('#updateTov2').val(data.reservation.To);

  document.getElementById('persons_update').dispatchEvent(new Event('change'));
  document
    .getElementById('persons_update_slider')
    .dispatchEvent(new Event('change'));
};
jQuery('#modify_order_number').on('input', function () {
  var value = jQuery(this).val();
  var newValue = value.replace(/\D/g, '');
  jQuery(this).val(newValue);
});
jQuery('#cancel_order_number').on('input', function () {
  var value = jQuery(this).val();
  var newValue = value.replace(/\D/g, '');
  jQuery(this).val(newValue);
});

// hadle step-from

const handleStepForm = () => {
  const block = document.getElementsByClassName('redi-layout')[0];
  const steps = document.getElementsByClassName('redi-step-form__step');
  const buttons = document.getElementsByClassName('redi-step-form__nav-button');
  const indicatorsContainer = document.getElementsByClassName(
    'redi-steps-indicator'
  )[0];
  const indicators = document.getElementsByClassName(
    'redi-steps-indicator__step'
  );

  if (
    block === undefined ||
    !buttons.length ||
    !steps.length ||
    !indicators.length ||
    indicatorsContainer === undefined
  )
    return;

  const removeAllActiveClasses = () => {
    [...buttons].forEach((button) => button.classList.remove('active'));
    [...steps].forEach((route) => route.classList.remove('active'));
  };

  const setActiveIndicator = (currentStep) => {
    [...indicators].forEach((indicator) =>
      indicator.classList.remove('active')
    );

    [...indicators].forEach((indicator) => {
      const step = indicator.getAttribute('data-redi-step');
      if (step === currentStep) {
        indicator.classList.add('active');
        indicatorsContainer.setAttribute('data-redi-current-step', step);
      }
    });
  };

  [...buttons].forEach((button) => {
    let hasTrueValue = false;

    button.addEventListener('click', (event) => {
      event.preventDefault();
      const currentStep = button.getAttribute('data-redi-step');

      if (currentStep == 2) {
        if (document.querySelector('.redi-calendar:not(.active)')) {
          qeryDataCalendarGetWork();
        }
        removeAllActiveClasses();
      }

      if (currentStep == 3) {
        const nextFormButton = document.querySelector(
          '.redi-button--primary.redi-step-form__nav-button[data-redi-step="4"]'
        );
        const formInputs = document.querySelectorAll(
          '.redi-contact-info__fields .redi-field--required input, .redi-contact-info__fields .redi-field--required textarea'
        );
        const formFields = document.querySelectorAll(
          '.redi-contact-info__fields .redi-field--required'
        );

        if (!formInputs.length || !formFields.length) return;

        formInputs.forEach((formInput) => {
          const field = formInput.closest('.redi-field');

          if (field === null) return;

          formInput.addEventListener('change', () => {
            if (
              (formInput.type === 'radio' || formInput.type === 'checkbox') &&
              formInput.checked
            ) {
              field.classList.add('has-interaction');
            } else if (
              formInput.type !== 'radio' &&
              formInput.type !== 'checkbox' &&
              formInput.value !== ''
            ) {
              field.classList.add('has-interaction');
            } else {
              field.classList.remove('has-interaction');
            }

            if (
              [...formFields].every((formField) =>
                formField.classList.contains('has-interaction')
              )
            ) {
              nextFormButton.removeAttribute('disabled');
            } else {
              nextFormButton.setAttribute('disabled', true);
            }
          });
        });
      }

      if (currentStep == 4) {
        if (Object.keys(window.validation.key).length === 0) {
          window.handleFormFields(
            '.redi-contact-info__fields .redi-field--required',
            '#contacts_error_message',
            '.redi-button--primary.redi-step-form__nav-button[data-redi-step="4"]',
            true
          );
        } else {
          window.handleFormFields(
            '.redi-contact-info__fields .redi-field--required',
            '#contacts_error_message',
            '.redi-button--primary.redi-step-form__nav-button[data-redi-step="4"]'
          );
        }

        hasTrueValue = Object.values(window.validation.key).some(
          (value) => value === true
        );

        if (Object.keys(window.validation.key).length === 0 || hasTrueValue)
          return;

        makeReservationSet((response) => {
          if (response.Error) {
            cancelOrderMessageError(response.Error, 'contacts_error_message');
          } else {
            if (redirect_to_confirmation_page.length > 0) {
              jQuery(location).attr(
                'href',
                redirect_to_confirmation_page +
                  '?reservation_id=' +
                  addSpace(response['ID'])
              );
            } else {
              document.getElementById('reservation-id').innerHTML =
                '#' + `${response['ID']}`.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
              removeAllActiveClasses();
              [...steps].forEach((step) => {
                if (currentStep === step.getAttribute('data-redi-step')) {
                  button.classList.add('active');
                  step.classList.add('active');
                  setActiveIndicator(currentStep);
                  block.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                  });
                }
              });
            }
          }
        });

        return;
      }

      if (currentStep != 4) {
        removeAllActiveClasses();
        [...steps].forEach((step) => {
          if (currentStep === step.getAttribute('data-redi-step')) {
            button.classList.add('active');
            step.classList.add('active');
            setActiveIndicator(currentStep);
            block.scrollIntoView({
              behavior: 'smooth',
              block: 'start',
            });
          }
        });
      }
    });
  });
};

// handle place-select

const handlePlaceSelect = () => {
  const container = document.getElementsByClassName('redi-place-select');

  if (!container.length) return;

  [...container].forEach((container) => {
    const containerBody = container.getElementsByClassName(
      'redi-place-select__body'
    )[0];
    const select = container.getElementsByTagName('select')[0];
    const options = container.getElementsByTagName('option');

    if (containerBody === undefined || select === undefined || !options.length)
      return;

    let fakeOptions = '';
    let isDropdownOpen = false;
    let currentOptionIndex = 0;

    const toggleDropdown = () => {
      container.classList.toggle('open');
      isDropdownOpen = !isDropdownOpen;
    };

    [...options].forEach((option) => {
      fakeOptions += `
				<li data-value="${option.value}" class="redi-place-select__option">
					<div class="redi-place-select__item">
						<span class="redi-place-select__subtitle">${option.textContent}</span>
						<span class="redi-place-select__address">${option.getAttribute(
              'data-address'
            )}</span>
					</div>
				</li>
			`;
    });

    containerBody.innerHTML += `
			<svg class="redi-icon">
				<use xlink:href="${DIR_URL}/wp-content/plugins/redi-restaurant-reservation/img/v2/svg/sprite.svg#chevron-down"></use>
			</svg>

			<div class="redi-place-select__current">
				<span class="redi-place-select__subtitle">Please select place</span>
			</div>

			<div class="redi-place-select__dropdown">
				<ul class="redi-place-select__list">
					${fakeOptions}
				</ul>
			</div>
		`;
    fakeOptions = container.getElementsByClassName('redi-place-select__option');
    const current = container.getElementsByClassName(
      'redi-place-select__current'
    )[0];

    if (!fakeOptions.length || current === undefined) return;

    [...fakeOptions].forEach((fakeOption, index) => {
      fakeOption.addEventListener('click', () => {
        [...fakeOptions].forEach((fakeOption) =>
          fakeOption.classList.remove('selected')
        );

        [...options].forEach((option) => {
          if (option.value === fakeOption.getAttribute('data-value')) {
            option.selected = true;
            select.setAttribute(
              'data-address',
              option.getAttribute('data-address')
            );
            select.value = option.value;
            select.dispatchEvent(new Event('change'));
          }
        });
        current.innerHTML = fakeOption.innerHTML;
        fakeOption.classList.add('selected');
        current.classList.add('selected');
        currentOptionIndex = index;
        isDropdownOpen = !isDropdownOpen;
        getCustomFields(fakeOption.dataset.value);
        document.querySelector('.redi-timepicker').innerHTML = '';
      });
    });

    const focusCurrentOption = () => {
      const currentOption = fakeOptions[currentOptionIndex];
      currentOption.classList.add('highlithed');
      [...fakeOptions].forEach((fakeOption) => {
        if (fakeOption !== currentOption) {
          fakeOption.classList.remove('highlithed');
        }
      });
    };

    const moveFocusDown = () => {
      if (currentOptionIndex < fakeOptions.length - 1) {
        currentOptionIndex++;
      } else {
        currentOptionIndex = 0;
      }
      focusCurrentOption();
    };

    const moveFocusUp = () => {
      if (currentOptionIndex > 0) {
        currentOptionIndex--;
      } else {
        currentOptionIndex = fakeOptions.length - 1;
      }
      focusCurrentOption();
    };

    const selectCurrentOption = () => {
      const selectedOption = fakeOptions[currentOptionIndex];
      selectOptionByElement(selectedOption);
    };

    const selectOptionByElement = (fakeOption) => {
      [...fakeOptions].forEach((fakeOption) => {
        fakeOption.classList.remove('selected');
        fakeOption.classList.remove('highlithed');
      });

      [...options].forEach((option) => {
        if (option.value === fakeOption.getAttribute('data-value')) {
          option.selected = true;
        }
      });

      current.innerHTML = fakeOption.innerHTML;
      fakeOption.classList.add('selected');
      current.classList.add('selected');
      toggleDropdown();
    };

    const handleKeyPress = (event) => {
      event.preventDefault();
      const { key } = event;
      const openKeys = ['ArrowDown', 'ArrowUp', 'Enter', 'Space', ' '];

      if (key === 'Tab') {
        containerBody.blur();
      }

      if (!isDropdownOpen && openKeys.includes(key)) {
        toggleDropdown();
      } else if (isDropdownOpen) {
        switch (key) {
          case 'Escape':
            toggleDropdown();
            break;
          case 'ArrowDown':
            moveFocusDown();
            break;
          case 'ArrowUp':
            moveFocusUp();
            break;
          case 'Enter':
          case ' ':
            selectCurrentOption();
            break;
          default:
            break;
        }
      }
    };

    const handleDocumentInteraction = (event) => {
      if (!event.composedPath().includes(container)) {
        container.classList.remove('open');
        isDropdownOpen = !isDropdownOpen;
      }
    };

    container.addEventListener('click', toggleDropdown);
    container.addEventListener('keydown', handleKeyPress);
    document.addEventListener('click', handleDocumentInteraction);
  });
};

// handle phone inputs
let itiInstances = [];
window.handlePhoneInputs = (
  initialCountry = null,
  onlyCoutries = null,
  destroy = false
) => {
  const inputs = document.querySelectorAll('.redi-field--phone input');

  if (!inputs.length) return;

  inputs.forEach((input) => {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/[^0-9]/g, '');
    });

    const iti = window.intlTelInput(input, {
      utilsScript:
        'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.0/build/js/utils.js',
      strictMode: true,
      useFullscreenPopup: false,
      countrySearch: false,
      separateDialCode: true,
      onlyCountries: onlyCoutries !== null ? onlyCoutries : false,
      initialCountry: initialCountry !== null ? initialCountry : 'auto',
    });

    itiInstances.push({ input: input, iti: iti });
    stepFormState.redi_fonecode = '+' + iti.s.dialCode;

    input.addEventListener('countrychange', () => {
      input.setAttribute('data-value', '');
      stepFormState.redi_fonecode = '+' + iti.s.dialCode;
      input.dispatchEvent(new Event('change'));
    });
  });
};

// handle redi-field--select
window.handleSelects = () => {
  const selects = document.getElementsByClassName('redi-field__select');

  if (!selects.length) return;

  [...selects].forEach((select) => {
    const instance = NiceSelect.bind(select);

    instance.dropdown.addEventListener('click', (event) => {
      if (event.target.classList.contains('option')) {
        instance.dropdown.classList.add('selected');

        if (instance.dropdown.closest('.redi-field')) {
          instance.dropdown
            .closest('.redi-field')
            .classList.add('has-interaction');
        }
      }
    });
  });
};

// handle popup

const handlePopup = () => {
  const body = document.getElementsByClassName('redi-app')[0] || document.body;
  const container = document.getElementsByClassName('redi-popup-container')[0];
  const openButtons = document.getElementsByClassName('redi-popup__button');
  const popups = document.getElementsByClassName('redi-popup');

  if (!openButtons.length || !popups.length || container === undefined) return;

  const openPopup = () => {
    container.classList.add('active');
    body.classList.add('redi-scroll-lock');
  };

  const closePopup = () => {
    container.innerHTML = '';
    container.classList.remove('active');
    body.classList.remove('redi-scroll-lock');
  };

  const handleCloseButtons = (popup) => {
    const closeButtons = popup.getElementsByClassName(
      'redi-popup__close-button'
    );

    if (!closeButtons.length) return;

    [...closeButtons].forEach((closeButton) => {
      closeButton.addEventListener('click', (event) => {
        event.preventDefault();
        closePopup();
      });
    });
  };

  container.addEventListener('click', closePopup);

  [...openButtons].forEach((openButton) => {
    openButton.addEventListener('click', (event) => {
      event.preventDefault();

      const popupId = openButton.getAttribute('href').replace('#', '');

      [...popups].forEach((popup) => {
        if (popupId === popup.id) {
          const clonePopup = popup.cloneNode(true);
          clonePopup.addEventListener('click', (event) =>
            event.stopPropagation()
          );
          container.appendChild(clonePopup);
          handleCloseButtons(clonePopup);
          openPopup();
        }
      });
    });
  });
};

// handle range slider
const handleRangeSlider = () => {
  const alertMessage = document.getElementById('range_alert_message');
  const ranges = document.getElementsByClassName('redi-range');

  if (!ranges.length) return;

  [...ranges].forEach((range) => {
    const rangeInput = range.getElementsByClassName(
      'redi-range__range-input'
    )[0];

    const rangeLabels = range.getElementsByClassName('redi-range__label');
    const fieldInput = range.getElementsByClassName('redi-range__input')[0];
    const incrementButton = range.getElementsByClassName(
      'redi-range__increment-button'
    )[0];

    const decrementButton = range.getElementsByClassName(
      'redi-range__decrement-button'
    )[0];

    const progressBar = range.getElementsByClassName('redi-range__progress')[0];

    if (
      rangeInput === undefined ||
      !rangeLabels.length ||
      fieldInput === undefined ||
      incrementButton === undefined ||
      decrementButton === undefined ||
      progressBar === undefined
    )
      return;

    const maxValue = parseInt(range.getAttribute('data-redi-max'));
    const minValue = parseInt(range.dataset.rediMin);

    const maxLabelValue = parseInt(rangeLabels[1].textContent);
    let currentValue = parseInt(rangeInput.value);

    const handleControls = (currentValue) => {
      if (currentValue < minValue) {
        rangeInput.value = minValue;
        fieldInput.value = minValue;
        progressBar.style.width = 0 + '%';
        return;
      }

      if (currentValue >= maxLabelValue) {
        rangeLabels[1].classList.add('active');
        rangeLabels[0].classList.remove('active');
      } else if (currentValue === minValue) {
        fieldInput.value = minValue;
        rangeLabels[0].classList.add('active');
        rangeLabels[1].classList.remove('active');
      } else {
        fieldInput.value = currentValue;
        rangeLabels[0].classList.remove('active');
        rangeLabels[1].classList.remove('active');
      }

      progressBar.style.width =
        ((currentValue - minValue) / (maxLabelValue - minValue)) * 100 + '%';
    };

    const handleAlertMessage = () => {
      if (
        alertMessage !== null &&
        range.classList.contains('redi-range--persons')
      ) {
        if (currentValue >= maxLabelValue) {
          alertMessage.classList.remove('redi-message--hidden');
        } else {
          alertMessage.classList.add('redi-message--hidden');
        }
      }
    };

    const changeAll = () => {
      currentValue = parseInt(fieldInput.value);
      rangeInput.value = currentValue;
      rangeInput.setAttribute('data-value', currentValue);
      handleControls(currentValue);
      handleAlertMessage();
      rangeInput.dispatchEvent(new Event('change'));
    };

    rangeInput.addEventListener('input', () => {
      currentValue = parseInt(rangeInput.value);
      rangeInput.setAttribute('data-value', currentValue);
      handleControls(currentValue);
      rangeInput.dispatchEvent(new Event('change'));

      if (currentValue >= maxLabelValue) {
        fieldInput.value = maxLabelValue;
      }

      handleAlertMessage();
    });

    fieldInput.addEventListener('blur', () => {
      let cleanValue =
        fieldInput.value.length !== '' &&
        fieldInput.value.replace(/\D/g, '') !== ''
          ? fieldInput.value.replace(/\D/g, '')
          : minValue;
      cleanValue = cleanValue >= maxValue ? maxValue : cleanValue;

      if (maxValue !== null) {
        fieldInput.value = cleanValue >= maxValue ? maxValue : cleanValue;
      } else {
        fieldInput.value = cleanValue;
      }

      currentValue = parseInt(fieldInput.value);
      rangeInput.value = currentValue;
      handleControls(currentValue);
      fieldInput.dispatchEvent(new Event('change'));
    });

    const incementValue = () => {
      if (maxValue !== null && fieldInput.value >= maxValue) return;
      fieldInput.value = parseInt(fieldInput.value) + 1;

      changeAll();
    };

    const decrementValue = () => {
      if (minValue !== null && fieldInput.value <= minValue) return;
      fieldInput.value = parseInt(fieldInput.value) - 1;

      changeAll();
    };

    const handleKeyPress = (event) => {
      const { key } = event;

      if (['ArrowDown', 'ArrowUp'].includes(key)) {
        switch (key) {
          case 'ArrowDown':
            decrementValue();
            break;
          case 'ArrowUp':
            incementValue();
            break;
          default:
            break;
        }
      } else if (key === 'Tab') {
        event.target.blur();
      }
    };

    incrementButton.addEventListener('click', (event) => {
      event.preventDefault();
      incementValue();
    });

    decrementButton.addEventListener('click', (event) => {
      event.preventDefault();
      decrementValue();
    });

    fieldInput.addEventListener('keydown', handleKeyPress);
    handleControls(currentValue);
  });
};

// set caledar message
window.setCalendarMessage = (text, isError = false) => {
  return `
		<div class="redi-message ${isError === true ? 'redi-message--error' : ''}">
				<div class="redi-message__arrow"></div>
			<p>${text}</p>
		</div>
	`;
};

// handle calendar

window.handleDatepicker = (popups = {}, language = locale) => {
  const calendarElement = document.getElementsByClassName('redi-calendar')[0];

  if (calendarElement === undefined) return;

  calendarElement.name = 'redi_date';

  const options = {
    type: 'default',
    settings: {
      range: {
        disablePast: true,
      },
      lang: language,
    },

    popups: popups,
    actions: {
      clickArrow(e, self) {},
      clickDay(e, self) {
        calendarElement.value = self.selectedDates;
        calendarElement.dispatchEvent(new Event('change'));

        const errorMessage = document.getElementById(
          'datepicker_error_message'
        );
        const nextButton = document.querySelector(
          '.redi-button--primary.redi-step-form__nav-button[data-redi-step="3"]'
        );

        if (
          errorMessage !== null &&
          !errorMessage.classList.contains('redi-message--hidden') &&
          nextButton !== null
        ) {
          errorMessage.classList.add('redi-message--hidden');
          nextButton.removeAttribute('disabled');
        }

        setTimeout(() => {
          const button = document.querySelector(
            '.vanilla-calendar-day__btn_selected'
          );
          if (button === null) return;

          const popup = button.parentElement.querySelector(
            '.vanilla-calendar-day__popup'
          );

          if (popup === null) return;

          const popupArrow = popup.querySelector('.redi-message__arrow');

          if (popupArrow === null) return;

          const position = findSpecialNumber(
            getElementPositionFromEdges(popup)
          );

          if (position === null) return;

          position.value = Math.abs(position.value);

          if (position.key === 'right') {
            popup.style.left = `calc(50% - ${position.value + 10}px)`;
            popupArrow.style.left = `calc(50% + ${position.value + 5}px)`;
          }

          if (position.key === 'left') {
            popup.style.left = `calc(50% + ${position.value + 10}px)`;
            popupArrow.style.left = `calc(50% - ${position.value + 15}px)`;
          }
          // }
        }, 0);

        var data = {
          action: 'redi_restaurant-submit',
          get: 'step1',
          placeID: jQuery('#place_select').val(),
          // startTime: jQuery('#redi-restaurant-startTime-alt').val(),
          startDateISO: self.selectedDates[0],
          duration: jQuery('#duration').val(),
          persons:
            +jQuery('#persons').val() +
            (jQuery('#children').val() ? +jQuery('#children').val() : 0),
          lang: language,
          // timeshiftmode: timeshiftmode,
          apikeyid: apikeyid,
        };

        function makeReservation(data) {
          return new Promise(function (resolve, reject) {
            jQuery.post(
              redi_restaurant_reservation.ajaxurl,
              data,
              function (response) {
                if (response['Error'] !== undefined) {
                  if (
                    errorMessage !== null &&
                    errorMessage.classList.contains('redi-message--hidden') &&
                    nextButton !== null
                  ) {
                    errorMessage.classList.remove('redi-message--hidden');
                    nextButton.setAttribute('disabled', true);
                    // nextButton.removeAttribute('disabled');
                  }
                } else if (response['all_booked_for_this_duration']) {
                  // Handle fully booked case if needed
                  // reject(redi_restaurant_reservation.error_fully_booked);
                } else {
                  if (response['alternativeTime'] !== undefined) {
                    let numIdTime = 0;
                    switch (response['alternativeTime']) {
                      case 1:
                        // Handle case 1 if needed
                        break;
                      case 2:
                        // Handle case 2 if needed
                        break;
                      case 3:
                        if (response[0].Name == null) {
                          document.querySelector('.redi-timepicker').innerHTML =
                            '';

                          createContainerSelectTime();
                          createButtonSelectTime(
                            stepFormState.redi_titleButton,
                            'all',
                            'active'
                          );
                          createTabSelectTime(
                            response[0].Availability,
                            'all',
                            numIdTime,
                            'active',
                            'active'
                          );

                          jQuery('.redi-timepicker__body').append(
                            '<div class="services-left"></div>'
                          );
                        } else {
                          document.querySelector('.redi-timepicker').innerHTML =
                            '';

                          createContainerSelectTime();
                          createButtonSelectTime(
                            stepFormState.redi_titleButton,
                            'all',
                            'active'
                          );

                          for (const key in response) {
                            if (Object.hasOwnProperty.call(response, key)) {
                              const element = response[key];

                              if (key !== 'alternativeTime') {
                                nowTab = key === '0' ? true : false;

                                createTabSelectTime(
                                  element.Availability,
                                  'all',
                                  (key + key + 554848) * 2,
                                  'active',
                                  nowTab
                                );
                                createButtonSelectTime(
                                  element.Name,
                                  element.Name,
                                  ''
                                );
                                createTabSelectTime(
                                  element.Availability,
                                  element.Name,
                                  key,
                                  '',
                                  'active'
                                );
                              }
                            }
                          }
                          jQuery('.redi-timepicker__body').append(
                            '<div class="services-left"></div>'
                          );

                          selectTabButton();
                        }
                        break;
                    }
                  } else {
                    // Handle default case if needed
                  }
                }
                // After completing the request and handling response
                resolve(response);
              },
              'json'
            );
          });
        }
        makeReservation(data)
          .then(function (response) {
            // Call handleReservationDetails() after successful reservation
            handleReservationDetails();
          })
          .catch(function (error) {
            // Handle any errors if necessary
            console.error('Reservation error:', error);
          });
      },
    },
  };

  const calendar = new VanillaCalendar('.redi-calendar', options);
  calendar.init();
  document.querySelector('.redi-calendar').classList.add('active');
};

// create tab boxes

const createContainerSelectTime = () => {
  jQuery('.redi-timepicker').append(
    '<div class="redi-timepicker__body"></div>'
  );
  jQuery('.redi-timepicker__body').append(
    '<div class="redi-timepicker__nav"></div>'
  );
  jQuery('.redi-timepicker__nav').append(
    '<div class="redi-timepicker__inner"></div>'
  );
  jQuery('.redi-timepicker__body').append(
    '<div class="redi-timepicker__tabs"></div>'
  );
};

const createButtonSelectTime = (title, id, active) => {
  jQuery('.redi-timepicker__inner').append(
    '<button class="redi-timepicker__nav-button ' +
      active +
      '" data-redi-time="' +
      id +
      '">' +
      title +
      '</button>'
  );
};

// create item times
const createTabSelectTime = (response, dataTab, numIdTime, active, nowTab) => {
  if (nowTab) {
    jQuery('.redi-timepicker__tabs').append(
      '<div class="redi-timepicker__tab ' +
        active +
        '" data-redi-time="' +
        dataTab +
        '"></div>'
    );
  }

  let html = '';

  [...response].forEach((b, c) => {
    let picker = '';
    let endPicker = '';
    let endTime = '';

    if (b.StartTime.split(' ')[1]) {
      picker =
        '<span class="redi-timepicker__daytime">' +
        b.StartTime.split(' ')[1] +
        '</span>';
    }

    if (redi_restaurant_reservation.endreservationtime) {
      endTime =
        '<span class="redi-timepicker__time">' +
        b.EndTime.split(' ')[0] +
        '</span>';

      if (b.EndTime.split(' ')[1]) {
        endPicker =
          '<span class="redi-timepicker__daytime">' +
          b.EndTime.split(' ')[1] +
          '</span>';
      }
    }

    let availableCont = displayLeftSeats
      ? 'data-available="' + b.ServicesLeft + '"'
      : '';

    if (b.Available) {
      html +=
        '<label for="time_breakfast_' +
        (c + 1) +
        numIdTime +
        '" class="redi-timepicker__button">' +
        '<input ' +
        availableCont +
        ' type="radio" form="redi_step_form" value="' +
        b.StartTime +
        `${
          redi_restaurant_reservation.endreservationtime
            ? ' - ' + b.EndTime
            : ''
        }` +
        '" id="time_breakfast_' +
        (c + 1) +
        numIdTime +
        '" name="redi_time">' +
        '<span class="redi-timepicker__content">' +
        '<span class="redi-timepicker__time">' +
        b.StartTime.split(' ')[0] +
        '</span>' +
        picker +
        endTime +
        endPicker +
        '</span></label>';
    } else if (!b.Available && b.ReservationRuleId === 3) {
      html +=
        '<label for="time_breakfast_' +
        (c + 1) +
        numIdTime +
        '" class="redi-timepicker__button" data-error-text="' +
        b.Reason +
        '">' +
        '<input type="radio" form="redi_step_form" id="time_breakfast_' +
        (c + 1) +
        numIdTime +
        '" name="redi_time" disabled="" value="' +
        b.StartTime +
        `${
          redi_restaurant_reservation.endreservationtime
            ? ' - ' + b.EndTime
            : ''
        }` +
        '">' +
        '<span class="redi-timepicker__content">' +
        '<span class="redi-timepicker__time">' +
        b.StartTime.split(' ')[0] +
        '</span>' +
        picker +
        endTime +
        endPicker +
        '</span></label>';
    } else if (!b.Available && b.ReservationRuleId === 0) {
      html +=
        '<label for="time_breakfast_' +
        (c + 1) +
        numIdTime +
        '" class="redi-timepicker__button redi-timepicker__button--orange" data-error-text="' +
        b.Reason +
        '">' +
        '<input type="radio" form="redi_step_form" id="time_breakfast_' +
        (c + 1) +
        numIdTime +
        '" name="redi_time" disabled="" value="' +
        b.StartTime +
        `${
          redi_restaurant_reservation.endreservationtime
            ? ' - ' + b.EndTime
            : ''
        }` +
        '">' +
        '<span class="redi-timepicker__content">' +
        '<span class="redi-timepicker__time">' +
        b.StartTime.split(' ')[0] +
        '</span>' +
        picker +
        endTime +
        endPicker +
        '</span></label>';
    }
    numIdTime++;
  });
  jQuery('.redi-timepicker__tab[data-redi-time="' + dataTab + '"]').append(
    html
  );
  jQuery('.redi-timepicker__tab[data-redi-time="time"]').append(html);
};

// handle scroll slider

const handleTabsNavWidth = () => {
  const nav = document.getElementsByClassName('redi-timepicker__nav')[0];

  if (nav === undefined) return;

  const setNavWidth = () => {
    if (window.innerWidth <= 465 || window.innerWidth <= nav.scrollWidth) {
      nav.style.width = window.innerWidth - 20 + 'px';
    }
  };

  setNavWidth();
  window.addEventListener('resize', throttled(300, setNavWidth));
};

const selectTabButton = () => {
  const button = document.querySelectorAll('button[data-redi-time]');
  const tab = document.querySelectorAll('.redi-timepicker__tab');

  if (button.length > 1) {
    [...button].forEach((but, index) => {
      but.addEventListener('click', () => {
        [...button].forEach((route) => route.classList.remove('active'));
        [...tab].forEach((route) => route.classList.remove('active'));
        but.classList.add('active');
        tab[index].classList.add('active');
      });
    });
  }
};

// handle redi-field
window.handleFormFields = (
  containersSelector,
  messagesContainerSelector,
  nextButtonSelector = null,
  isCheckValidation = false
) => {
  const containers = document.querySelectorAll(containersSelector);
  const errorMessage = document.querySelector(messagesContainerSelector);
  const nextButton = document.querySelector(nextButtonSelector);

  window.validation = {
    ...window.validation,
    containers: containers,
    errorMessage: errorMessage,
    nextButton: nextButton,
  };

  if (!containers.length || errorMessage === null) return;

  const errorMessageContent = errorMessage.getElementsByClassName(
    'redi-message__content'
  )[0];

  if (errorMessageContent === undefined) return;

  let hasInvalidFields = {};

  const checkIsHasInvalidFields = () => {
    window.validation.key = hasInvalidFields;

    errorMessageContent.innerHTML = '';

    if (Object.values(hasInvalidFields).some((value) => value === true)) {
      if (nextButton !== null) {
        nextButton.setAttribute('disabled', true);
      }
      errorMessage.classList.remove('redi-message--hidden');

      for (let key in hasInvalidFields) {
        const input = document.querySelector(`[name=${key}]`);
        const field = input.closest('.redi-field');

        if (input === null || field === null) return;

        if (hasInvalidFields[key] === true) {
          errorMessageContent.innerHTML += `<p>${field.getAttribute(
            'data-error-message'
          )}</p>`;
          field.classList.add('redi-field--error');
        } else {
          field.classList.remove('redi-field--error');
        }
      }
    } else {
      if (nextButton !== null) {
        nextButton.removeAttribute('disabled');
      }
      errorMessage.classList.add('redi-message--hidden');
    }
  };

  const setInvalid = (container, key = null) => {
    container.classList.add('redi-field--error');

    if (key) {
      hasInvalidFields[key] = true;
      checkIsHasInvalidFields();
    }
  };

  const unsetInvalid = (container, key) => {
    container.classList.remove('redi-field--error');

    if (key) {
      hasInvalidFields[key] = false;
      checkIsHasInvalidFields();
    }
  };

  containers.forEach((container) => {
    const field = container.querySelector(
      'input:not([type=radio]):not([type=checkbox]):not(.iti__search-input), textarea, select'
    );
    const radios = container.querySelectorAll('[type=radio]');
    const checkbox = container.querySelector('[type=checkbox]');
    const select = container.querySelector('select');

    if (select !== null) {
      const niceSelect = container.querySelector('.nice-select');
      const options = container.querySelectorAll('.option');

      if (niceSelect === null || !options.length) return;

      hasInvalidFields[select.name] = niceSelect.classList.contains('selected')
        ? false
        : true;

      options.forEach((option) => {
        option.addEventListener('click', () => {
          unsetInvalid(container, select.name);
        });
      });
    }

    if (radios.length) {
      const isChecked = Array.from(radios).some((radio) => radio.checked);
      hasInvalidFields[radios[0].name] = isChecked ? false : true;

      radios.forEach((radio) => {
        radio.addEventListener('change', () => {
          unsetInvalid(container, radios[0].name);
        });
      });
    }

    if (checkbox !== null) {
      hasInvalidFields[checkbox.name] = checkbox.checked ? false : true;

      checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
          unsetInvalid(container, checkbox.name);
        } else {
          setInvalid(container, checkbox.name);
        }
      });
    }

    if (field !== null) {
      hasInvalidFields[field.name] = true;

      (checkField = (field) => {
        if (field.id === 'cancel_reservation') {
          if (field.value.length === 0 || field.value === '') {
            setInvalid(container, field.name);
          } else {
            unsetInvalid(container, field.name);
          }
          return;
        }

        if (field.type === 'email') {
          const emailPattern =
            /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
          if (!emailPattern.test(field.value)) {
            setInvalid(container, field.name);
            return;
          } else {
            unsetInvalid(container, field.name);
            return;
          }
        }

        if (field.name === 'redi_phone') {
          const instance = itiInstances.find((item) => item.input === field);

          if (instance) {
            const isValid = instance.iti.isValidNumber();

            const setPhoneIntoStepFormState = () => {
              foneNumber = removeSpaces(field.value);
              resultFone = removeStartString(
                foneNumber,
                '+' + instance.iti.s.dialCode
              );

              field.setAttribute('data-value', resultFone);

              field.dispatchEvent(new Event('change'));
            };

            if (!isValid) {
              setPhoneIntoStepFormState();
              setInvalid(container, field.name);
              return;
            } else {
              setPhoneIntoStepFormState();
              unsetInvalid(container, field.name);
              return;
            }
          }
        }

        if (
          field.name !== 'redi_phone' &&
          (field.value.length === 0 || field.value === '')
        ) {
          setInvalid(container, field.name);
          return;
        } else {
          unsetInvalid(container, field.name);
        }
      })(field);

      field.addEventListener('blur', () => {
        checkField(field);
      });
    }
  });

  if (isCheckValidation) {
    checkIsHasInvalidFields();
  }
};

// handle steps form next nax buttons

const handleStepsFormNextNavButtons = (
  nextButtons,
  backButtons,
  stepButtons
) => {
  if (
    stepFormState['redi_place'] !== null &&
    stepFormState['redi_persons'] !== null &&
    stepFormState['redi_persons'] !== '0 quest'
  ) {
    nextButtons[0].removeAttribute('disabled');
  } else {
    nextButtons[0].setAttribute('disabled', true);
  }

  if (
    stepFormState['redi_date'] !== null &&
    stepFormState['redi_time'] !== null
  ) {
    nextButtons[1].removeAttribute('disabled');
  } else {
    nextButtons[1].setAttribute('disabled', true);
  }

  const contactInfoBlocks = document.getElementsByClassName(
    'redi-view-reservation__contact-info'
  );
  if (!contactInfoBlocks.length) return;

  nextButtons[1].addEventListener('click', () => {
    [...contactInfoBlocks].forEach((element) => {
      element.classList.add('active');
    });
  });

  [...backButtons].forEach((backButton) => {
    backButton.addEventListener('click', () => {
      if (
        stepFormState['redi_name'] === null &&
        stepFormState['redi_phone'] === null &&
        stepFormState['redi_email'] === null
      ) {
        [...contactInfoBlocks].forEach((contactInfoBlock) => {
          contactInfoBlock.classList.remove('active');
        });
      }
    });
  });

  [...stepButtons].forEach((stepButton) => {
    stepButton.addEventListener('click', () => {
      if (
        stepFormState['redi_name'] === null &&
        stepFormState['redi_phone'] === null &&
        stepFormState['redi_email'] === null
      ) {
        [...contactInfoBlocks].forEach((contactInfoBlock) => {
          contactInfoBlock.classList.remove('active');
        });
      } else {
        [...contactInfoBlocks].forEach((contactInfoBlock) => {
          contactInfoBlock.classList.add('active');
        });
      }
    });
  });
};

// handle reservation-details
const handleReservationDetails = () => {
  const form = document.querySelector('form#redi_step_form');
  const fields = document.querySelectorAll('[form=redi_step_form]');

  const errorMessage = document.getElementById('datepicker_error_message');
  const items = document.querySelectorAll(
    '.redi-reservation-details .redi-view-reservation__item'
  );
  const nextButtons = document.querySelectorAll(
    '.redi-button--primary.redi-step-form__nav-button'
  );
  const backButtons = document.querySelectorAll(
    '.redi-button--underline.redi-step-form__nav-button'
  );
  const stepButtons = document.querySelectorAll(
    '.redi-steps-indicator__step.redi-step-form__nav-button'
  );

  if (
    form === null ||
    !fields.length ||
    !items.length ||
    !nextButtons.length ||
    !backButtons.length ||
    !stepButtons.length
  )
    return;

  const setValue = (formField, field, extraWords = null) => {
    if (extraWords !== null) {
      field.textContent = formField.getAttribute('data-value')
        ? formField.getAttribute('data-value') + ' ' + extraWords
        : formField.value + ' ' + extraWords;
    } else {
      field.textContent = formField.getAttribute('data-value')
        ? formField.getAttribute('data-value')
        : formField.value;
    }

    stepFormState[formField.name] = field.textContent;
    field.classList.remove('redi-empty');
  };

  const clearValue = (field, formField) => {
    field.textContent = field.getAttribute('data-default-text');
    stepFormState[formField.name] = null;
    field.classList.add('redi-empty');
  };

  const setServicesLeft = (available) => {
    document.querySelector('.services-left').innerHTML =
      redi_restaurant_reservation.available_seats + ': ' + available;
    document.querySelector('.services-left').style.display = 'block';
  };

  fields.forEach((formField) => {
    formField.addEventListener('change', (e) => {
      items.forEach((item) => {
        const fakefield = item.querySelector('[data-default-text-fake]');
        const field = item.querySelector('[data-default-text]');

        if (field === null) return;

        if (formField.name === field.id) {
          if (formField.name === 'redi_place') {
            const addressField = document.querySelector(
              '.redi-reservation-details .redi-view-reservation__item #redi_place_address'
            );

            if (addressField === null) return;

            addressField.textContent = formField.getAttribute('data-address');
            addressField.style.color = '#81858b';
            setValue(formField, field);
            if (fakefield) {
              fakefield.remove();
            }
            field.textContent =
              e.target.options[e.target.selectedIndex].dataset.title;
            field.style.color = '#171d29';
            return;
          }

          if (formField.name === 'redi_date') {
            const format = formField.getAttribute('data-date-format');
            const locale = formField.getAttribute('data-locale');

            const formattedDate = formatDate(
              formField.value,
              format ? format : '',
              locale ? locale : ''
            );

            field.textContent = formattedDate;
            stepFormState = {
              ...stepFormState,
              redi_display_date: formattedDate,
            };
            stepFormState[formField.name] = formatDate(formField.value);
            field.classList.remove('redi-empty');

            if (document.querySelector('.data_for_completed')) {
              document.querySelector('.data_for_completed').innerHTML =
                field.textContent;
            }
            return;
          }

          if (formField.name === 'redi_phone') {
            const instance = itiInstances.find(
              (item) => item.input === formField
            );
            if (!instance) {
              console.error('Instance not found for formField:', formField);
              return;
            }

            if (!instance.iti || !instance.iti.s) {
              console.error('Incomplete ITI instance:', instance);
              return;
            }
            foneNumber = removeSpaces(formField.value);

            resultFone = removeStartString(
              foneNumber,
              '+' + instance.iti.s.dialCode
            );

            formField.setAttribute('data-value', resultFone);

            field.textContent = formField.getAttribute('data-value');
            stepFormState[formField.name] = field.textContent;
            field.classList.remove('redi-empty');
            return;
          }

          if (typeof formField.value === 'object') {
            if (formField.value.length > 0) {
              setValue(formField, field);
            } else {
              clearValue(field, formField);
            }
            return;
          }

          if (typeof formField.value === 'string') {
            if (!isNaN(Number(formField.value))) {
              if (formField.value > 0) {
                if (formField.name === 'redi_persons') {
                  setValue(
                    formField,
                    field,
                    Number(formField.value) === 1 ? 'quest' : 'quests'
                  );
                } else if (formField.name === 'redi_children') {
                  setValue(
                    formField,
                    field,
                    Number(formField.value) === 1 ? 'child' : 'children'
                  );
                } else {
                  setValue(formField, field);
                }
              } else {
                clearValue(field, formField);
              }
            } else {
              if (formField.dataset.available) {
                setServicesLeft(formField.dataset.available);
              }

              if (formField.value !== '') {
                setValue(formField, field);

                const timeText = document.querySelector('.time_for_completed');
                const timeField = document.querySelector(
                  'input[type=radio]:checked + .redi-timepicker__content'
                );

                if (timeText !== null && timeField !== null) {
                  timeText.innerHTML = timeField.innerText.replace('/n', ' - ');
                }
              } else {
                clearValue(field, formField);
              }
            }
          }
        }
      });

      handleStepsFormNextNavButtons(nextButtons, backButtons, stepButtons);
    });
    if (formField.parentElement.dataset.errorText !== undefined) {
      formField.parentElement.addEventListener('click', () => {
      });
    }
  });
};


const handleRating = () => {
  const ratings = document.querySelectorAll('.redi-rating');

  if (!ratings.length) return;
  const setRatingActiveWidth = (ratingActive, ratingValue) => {
    ratingActive.style.width = ratingValue / 0.05 + '%';
  };

  const setRating = (rating, ratingActive) => {
    const ratingItems = rating.querySelectorAll('.redi-rating__item');

    if (!ratingItems.length) return;

    for (let index = 0; index < ratingItems.length; index++) {
      const ratingItem = ratingItems[index];

      ratingItem.addEventListener('mouseenter', function () {
        setRatingActiveWidth(ratingActive, ratingItem.value);
      });

      ratingItem.addEventListener('mouseleave', function () {
        setRatingActiveWidth(ratingActive, rating.getAttribute('data-value'));
      });

      ratingItem.addEventListener('click', function () {
        rating.setAttribute('data-value', index + 1);
        setRatingActiveWidth(ratingActive, index + 1);

        const ratingValue = rating.getAttribute('data-value');
        const hiddenInputs = document.querySelectorAll('.rating-score input');

        if (hiddenInputs.length) {
          hiddenInputs.forEach(
            (hiddenInput) => (hiddenInput.value = ratingValue)
          );
        }
      });
    }
  };
  ratings.forEach((rating) => {
    const ratingValue = rating.getAttribute('data-value');
    const ratingActive = rating.querySelector('.redi-rating__active');

    if (ratingActive === null || ratingValue === null) return;
    setRatingActiveWidth(ratingActive, ratingValue);
    if (rating.classList.contains('redi-rating-to-set')) {
      setRating(rating, ratingActive, ratingValue);
    }
  });
};

const linkWaitlistForm = () => {
  const button = document.querySelector('.link-waitlist-form');
  if (button) {
    button.addEventListener('click', () => {
      const errorMessage = document.querySelector('#white_list_error_message');

      if (errorMessage !== null) {
        errorMessage.classList.add('redi-message--hidden');
      }

      button.closest('.redi-layout__route').classList.remove('active');
      document.getElementById('white_listDateFrom').innerHTML =
        stepFormState.redi_display_date;
      document.getElementById('white_listPersonFrom').innerHTML =
        stepFormState.redi_persons;
      document
        .querySelector(
          '.redi-white_list-reservation[data-redi-route="white_list_reservation"]'
        )
        .classList.add('active');
    });
  }
};

function getCurrentDateMon(addMonths = 0) {
  let today = new Date();
  today.setMonth(today.getMonth() + addMonths);

  let year = today.getFullYear();
  let month = String(today.getMonth() + 1).padStart(2, '0');
  let day = String(today.getDate()).padStart(2, '0');

  return `${year}-${month}-${day}`;
}

const getDateFormFirstTable = () => {
  var data = {
    action: 'redi_restaurant-submit',
    get: 'date_information',
    from: getCurrentDateMon(),
    to: getCurrentDateMon(12),
    placeID: jQuery('#place_select').val(),
    apikeyid: apikeyid,
    guests:
      parseInt(jQuery('#persons').val()) +
      (jQuery('#children').val() === undefined
        ? 0
        : parseInt(jQuery('#children').val())),
  };
  return data;
};

const qeryDataCalendarGetWork = (successCallback) => {
  jQuery.post(
    redi_restaurant_reservation.ajaxurl,
    getDateFormFirstTable(),
    function (response) {
      data = JSON.parse(response);
      let transformedData = {};
      data.forEach((object) => {
        transformedData[object.Date] = {
          modifier: 'redi-closed',
          html: window.setCalendarMessage(object.Reason, true),
        };
      });
      handleDatepicker(transformedData, 'en-EN');
      if (typeof successCallback === 'function') {
        successCallback(response);
      }
    }
  );
};

function formatDateSet(inputDate) {
  const date = new Date(inputDate);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
}

function formatDateTime(dateInput, timeInput) {
  const date = new Date(dateInput);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');

  let [time, period = null] = timeInput.split(' ');
  let [hours, minutes] = time.split(':');

  if (period !== null) {
    hours = parseInt(hours, 10);
    if (period.toLowerCase() === 'pm' && hours !== 12) {
      hours += 12;
    }
    if (period.toLowerCase() === 'am' && hours === 12) {
      hours = 0;
    }
  }

  hours = String(hours).padStart(2, '0');
  minutes = String(minutes).padStart(2, '0');

  return `${year}-${month}-${day} ${hours}:${minutes}`;
}

function populateCustomFieldsValues(data) {
  let result = {};

  data.forEach(function (field) {
    if (field.Type != 'radio' && field.Type != 'options') {
      var element = document.getElementById('field_' + field.Id);
      if (element) {
        field.Value = element.value;
      }
      result['field_' + field.Id] = field.Value;
    } else if (field.Type == 'options') {
      var radios = document.getElementsByName('field_' + field.Id);
      [...radios].forEach((stepButton) => {
        if (stepButton.checked) {
          result['field_' + field.Id] = stepButton.value;
        }
      });
    }
  });
  return result;
}

const makeReservationSet = (successCallback) => {
  let firstName = '';
  let lastName = '';

  if (redi_restaurant_reservation.enablefirstlastname) {
    firstName = document.getElementById('firstName').value;
    lastName = document.getElementById('nameLastname').value;
  } else {
    firstName = document.getElementById('name').value;
    lastName = '';
  }

  var data = {
    action: 'redi_restaurant-submit',
    get: 'step3',
    startDate: formatDateSet(stepFormState.redi_date),
    startTime: formatDateTime(
      stepFormState.redi_date,
      stepFormState.redi_time.split(' - ')[0]
    ),
    persons: stepFormState.redi_persons,
    children:
      stepFormState.redi_children === null ? '0' : stepFormState.redi_children,
    UserName: firstName,
    UserLastName: lastName,
    UserEmail: stepFormState.redi_email,
    UserComments: stepFormState.redi_comments,
    UserPhone: stepFormState.redi_phone,
    placeID: stepFormState.redi_place,
    lang: locale,
    duration: jQuery('#duration').val(),
    apikeyid: apikeyid,
  };

  Object.assign(data, populateCustomFieldsValues(acf));

  jQuery
    .post(
      redi_restaurant_reservation.ajaxurl,
      data,
      function (response) {
        if (typeof successCallback === 'function') {
          successCallback(response);
        }
      },
      'json'
    )
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error('Error:', textStatus, errorThrown);
    });
};

// reservation cancel
const reservationCancel = (successCallback) => {
  var reservationNumber = jQuery('#cancel_order_number')
    .val()
    .replace(/\s+/g, '');
  var data = {
    action: 'redi_restaurant-submit',
    get: 'cancel',
    ID: reservationNumber,
    Name: jQuery('#redi_cancel_name').val(),
    Phone:
      jQuery('label[for=redi_cancel_phone] .iti__selected-dial-code').text() +
      jQuery('#redi_cancel_phone').val(),
    Email: jQuery('#redi_cancel_email').val(),
    Reason: jQuery('#redi_cancel_reason').val(),
    lang: locale,
    apikeyid: apikeyid,
  };
  jQuery.post(
    redi_restaurant_reservation.ajaxurl,
    data,
    function (response) {
      if (typeof successCallback === 'function') {
        successCallback(response);
      }
    },
    'json'
  );
};

// reservation Modifi
const reservationModifi = (successCallback) => {
  // trim spaces in number
  var reservationNumber = jQuery('#modify_order_number')
    .val()
    .replace(/\s+/g, '');

  var data = {
    action: 'redi_restaurant-submit',
    get: 'modify',
    ID: reservationNumber,
    Name: jQuery('#redi_modify_name').val(),
    Phone: stepFormState.redi_fonecode + jQuery('#redi_modify_phone').val(),
    Email: jQuery('#redi_modify_email').val(),
    lang: locale,
    apikeyid: apikeyid,
  };

  jQuery.post(
    redi_restaurant_reservation.ajaxurl,
    data,
    function (response) {
      if (typeof successCallback === 'function') {
        successCallback(response);
      }
    },
    'json'
  );
};

// reservation Update
const reservationUpdate = (successCallback) => {
  // Get the selected country code
  const countryCode = jQuery(
    '.redi-update-reservation__form .iti__selected-dial-code'
  )
    .text()
    .trim(); // Looking for the text of the selected country code

  // Getting a phone number
  const phoneNumber = jQuery('#redi_update_phone').val().trim();

  // Collect full phone number with country code
  const fullPhoneNumber = `${countryCode}${phoneNumber}`;

  var data = {
    action: 'redi_restaurant-submit',
    get: 'update',
    ID: jQuery('#redi-restaurant-updateIDv2').val(),
    PlaceReferenceId: jQuery('#updatePlaceReferenceIdv2').val(),
    Quantity: jQuery('#persons_update').val(),
    UserName: jQuery('#redi_update_name').val(),
    UserPhone: fullPhoneNumber, // Send full number with country code
    UserEmail: jQuery('#redi_update_email').val(),
    UserComments: jQuery('#redi_update_comments').val(),
    StartTime: jQuery('#updateFromv2').val(),
    EndTime: jQuery('#updateTov2').val(),
    lang: locale,
    apikeyid: apikeyid,
  };

  jQuery.post(
    redi_restaurant_reservation.ajaxurl,
    data,
    function (response) {
      if (typeof successCallback === 'function') {
        successCallback(response);
      }
    },
    'json'
  );
};

function getFormatDate(dateString) {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = ('0' + (date.getMonth() + 1)).slice(-2);
  const day = ('0' + date.getDate()).slice(-2);
  return `${year}-${month}-${day}`;
}

const whiteListSend = (successCallback) => {
  var data = {
    action: 'redi_waitlist-submit',
    get: 'waitlist',
    Date: getFormatDate(stepFormState.redi_date),
    Guests: jQuery('#persons').val(),
    Name: jQuery('#redi_white_list_name').val(),
    Email: jQuery('#redi_white_list_email').val(),
    Phone: jQuery('#redi_white_list_phone').val(),
    placeID: stepFormState.redi_place,
    Time: jQuery('#white_list_preferred_time').val(),
  };

  jQuery.post(
    redi_restaurant_reservation.ajaxurl,
    data,
    function (response, success) {
      if (typeof successCallback === 'function') {
        successCallback(response);
      }
    },
    'json'
  );
};

function getCustomFields(idPlace) {
  jQuery('#RediCustomFields').show();
  var data = {
    action: 'redi_restaurant-submit',
    get: 'get_custom_fields_v2',
    placeID: idPlace,
    lang: locale,
    apikeyid: apikeyid,
  };

  jQuery.post(redi_restaurant_reservation.ajaxurl, data, function (response) {
    const startTegForFileds = document.querySelector(
      '.redi-start-custom_fields'
    );

    if (response.success && response.data.custom_fields.length > 0) {
      acf = response.data.custom_fields_item.map((obj) => ({ ...obj }));
      renderCustomFields(response.data.custom_fields, startTegForFileds);
    } else if (response.success && response.data.custom_fields.length === 0) {
      removeRenderCustomFields(startTegForFileds);
    }
  });
}

const renderCustomFields = (data, startTegForFileds) => {
  removeRenderCustomFields(startTegForFileds);
  const div = document.createElement('div');
  div.innerHTML = data;
  startTegForFileds.after(...div.childNodes);
  window.handleSelects();
};

const removeRenderCustomFields = (startTegForFileds) => {
  let nextElement = startTegForFileds.nextSibling;
  while (nextElement) {
    let elementToRemove = nextElement;
    nextElement = nextElement.nextSibling;
    elementToRemove.remove();
  }
};

const closeElement = (element) => {
  element.classList.add('closed');
  element.style.display = 'none';
};

const openElement = (element) => {
  element.classList.remove('closed');
  element.style.display = null;
};

const cancelReservationClear = () => {
  const input = document.querySelectorAll(
    '.redi-cancel-reservation__form .redi-field input'
  );

  const inp = ['redi_cancel_email', 'redi_cancel_name', 'redi_cancel_phone'];

  input.forEach((el) => {
    if (inp.includes(el.id)) {
      el.addEventListener('input', () => {
        if (el.value === '') {
          input.forEach((item) => {
            if (item.closest('.redi-field').classList.contains('closed')) {
              openElement(item.closest('.redi-field'));
            }
          });
        } else {
          input.forEach((item) => {
            if (inp.includes(item.id) && item.value === '') {
              closeElement(item.closest('.redi-field'));
            }
          });
        }
      });
    }
  });
};

const modifyReservationClear = () => {
  const input = document.querySelectorAll(
    '.redi-modify-reservation__form .redi-field input'
  );

  const inp = ['redi_modify_email', 'redi_modify_name', 'redi_modify_phone'];

  input.forEach((el) => {
    if (inp.includes(el.id)) {
      el.addEventListener('input', () => {
        if (el.value === '') {
          input.forEach((item) => {
            if (item.closest('.redi-field').classList.contains('closed')) {
              openElement(item.closest('.redi-field'));
            }
          });
        } else {
          input.forEach((item) => {
            if (inp.includes(item.id) && item.value === '') {
              closeElement(item.closest('.redi-field'));
            }
          });
        }
      });
    }
  });
};

const parseUrl = () => {
  const url = window.location.href;
  const urlObj = new URL(url);

  if (!urlObj.hash.includes('#cancel') && !urlObj.hash.includes('#modify')) {
    return null;
  }

  const action = urlObj.hash.includes('#cancel') ? '#cancel' : '#modify';
  const queryString = urlObj.hash.split('?')[1];
  const params = new URLSearchParams(queryString);
  const reservationId = params.get('reservation');
  const personalInformation = params.get('personalInformation');

  if (reservationId && personalInformation) {
    return {
      action,
      reservationId,
      personalInformation,
    };
  } else {
    return null;
  }
};

const handleOnLoadUrl = () => {
  const data = parseUrl();

  if (data === null) return;

  if (data.action === '#modify') {
    const button = document.querySelector(
      'button[data-redi-route="modify_reservation"]'
    );
    const reservationIdField = document.querySelector(
      'form.redi-modify-reservation__form #modify_order_number'
    );
    const emailField = document.querySelector(
      'form.redi-modify-reservation__form #redi_modify_email'
    );

    if (button === null || reservationIdField === null || emailField === null)
      return;

    button.click();
    reservationIdField.value = data.reservationId;
    emailField.value = data.personalInformation;
  }

  if (data.action === '#cancel') {
    const button = document.querySelector(
      'button[data-redi-route="cancel_reservation"]'
    );
    const reservationIdField = document.querySelector(
      'form.redi-cancel-reservation__form #cancel_order_number'
    );
    const emailField = document.querySelector(
      'form.redi-cancel-reservation__form #redi_cancel_email'
    );

    if (button === null || reservationIdField === null || emailField === null)
      return;

    button.click();
    reservationIdField.value = data.reservationId;
    emailField.value = data.personalInformation;
  }
};

const main = () => {
  window.handleSelects();
  setParentContainerClass();
  handleReservationDetails();
  handleStepForm();
  handleTabs();
  handlePlaceSelect();
  handlePopup();
  handleTabsNavWidth();
  handleRangeSlider();
  handleRating();
  linkWaitlistForm();
  cancelReservationClear();
  modifyReservationClear();
  handleTabs('redi-route__button', 'redi-route', 'data-redi-route');
  handleTabs(
    'redi-timepicker__nav-button',
    'redi-timepicker__tab',
    'data-redi-time'
  );
  stepFormState;
  window.handlePhoneInputs('ee');
  handleOnLoadUrl();
};

window.onload = () => {
  main();
};
