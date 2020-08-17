$(function() {
	$(document).on('click', '.js-instruction-close', function (event) {
		event.preventDefault();
		$('.instruction').removeClass('visible');
		$('body').removeClass('overflow-hidden');
	});

	$(document).on('click', '.js-instruction-open', function (event) {
		event.preventDefault();
		$('.instruction').addClass('visible');
		$('body').addClass('overflow-hidden');
	});

	// событие для инфо споилеров
	$(document).on('click', '.info-spoiler__trigger', function (event) {
		event.stopPropagation();
		$(this).closest( ".info-spoiler" ).addClass('expanded');

	});

    function closeFooterLang() {
		if ($('.footer__lang').hasClass('opened')) {
			$('.footer__lang').removeClass("opened");
		}
	}

	function closeHeaderLang() {
		if ($('.header__lang').hasClass('opened')) {
			$('.header__lang').removeClass("opened");
		}
		if ($('header').hasClass('nav-opened')) {
			$('header').removeClass("nav-opened");
		}
	}

	function closeUserMenu() {
		if ($('.header__auth-user').find('.header__dropdown').hasClass('visible')) {
			$('.header__auth-user').find('.header__dropdown').removeClass("visible");
		}
	}

	function closeMenu() {
		if ($('.header__bars .icon-close').hasClass('icon-close')) {
			$('.nav').removeClass('visible');
			$('.header').removeClass('header_violet');
			$('.header__bars .icon-close').attr('class', 'icon icon-bars');
			$('body').removeClass('overflow-hidden');
		}
	}

	$(window).click(function(event) {
		/**
		 * @TODOD костылек для того чтобы не закрывалось при клике на ссылку
		 */
		if (event.target.nodeName === 'A') {
			return;
		}
		/**
		 * @TODOD костылек для того чтобы подсказка не закрывалось при клике на показать подсказку
		 */
		if ($(event.target).closest('.instruction-button').length == 0 && $(event.target).closest('.instruction').length == 0) {
			$('.instruction').removeClass('visible');
		}

		closeFooterLang();
		closeHeaderLang();
		closeUserMenu();
		closeMenu();
	});

    // Не скрывать меню при клике по нему
    $(document).on('click', 'nav.nav.top-menu', function (event) {
		event.stopPropagation();
	});
	$(document).on('click', '.header__langs', function (event) {
		event.stopPropagation();
	});

	// Показать языки
	$(document).on('click', '.header__lang, .nav__item_lang', function(event) {
		event.stopPropagation();
		closeFooterLang();
		closeUserMenu();
		closeMenu();
		$(this).toggleClass('opened');
		$('header').toggleClass('nav-opened');
	});

	$(document).on('click', '.header__langs-current', function() {
		closeFooterLang();
		closeUserMenu();
		closeMenu();
		$('.nav').addClass('visible');
		$('.header').addClass('header_violet');
		$('.header__bars .icon-bars').attr('class', 'icon icon-close');
		$('body').addClass('overflow-hidden');

		$('.header__lang, .nav__item_lang').toggleClass('opened');
		$('header').toggleClass('nav-opened');
	});

	// Показать выпадающе меню пользователя
	$(document).on('click', '.header__auth-user', function(event) {
		event.stopPropagation();
		closeFooterLang();
		closeHeaderLang();
		closeMenu();
		$(this).find('.header__dropdown').toggleClass('visible');
	});

	// Показать выпадающую навигацию
	$(document).on('click', '.header__bars .icon-bars', function(event) {
		event.stopPropagation();
		closeFooterLang();
		closeHeaderLang();
		closeUserMenu();
		$('.nav').addClass('visible');
		$('.header').addClass('header_violet');
		$(this).attr('class', 'icon icon-close');
		$('body').addClass('overflow-hidden');
	});

	$(document).on('click', '.header__bars .icon-close', function(event) {
		event.stopPropagation();
		closeFooterLang();
		closeHeaderLang();
		closeUserMenu();
		$('.nav').removeClass('visible');
		$('.header').removeClass('header_violet');
		$(this).attr('class', 'icon icon-bars');
		$('body').removeClass('overflow-hidden');
	});

	$(document).on('click', '.nav__list-heading', function(e) {
		e.stopPropagation();
		e.preventDefault();
		$('.nav__list.selected').each(function(){
			$(this).removeClass('selected');
		});
		if($(this).parent().hasClass('selected')) {
			$(this).parent().removeClass('selected');
		} else {
			$(this).parent().addClass('selected');
		}
	});

	$(document).on('click', '.footer__lang', function(event) {
		event.stopPropagation();
		closeHeaderLang();
		closeUserMenu();
		closeMenu();
		$(this).toggleClass('opened');
	});

	/* Footer menu */
	$(document).on('click', '.footer__block:not([data-expanded]), .footer__block[data-expanded="false"]', function() {
		$(this).attr('data-expanded', 'true');
		$(this).find('.footer__nav').show();
	});
	$(document).on('click', '.footer__block[data-expanded="true"]', function(e) {
		e.stopPropagation();
	});
	$(document).on('click', '.footer__heading', function() {
		if (window.matchMedia("(min-width: 567.98px)").matches) {
			return;
		}
		let $footerBlock = $(this).parents('.footer__block');
		$footerBlock.attr('data-expanded', function(index, attr) {
			return (typeof attr == 'undefined' || attr === 'false' || !attr);
		});
		$footerBlock.find('.footer__nav').toggle();
	});

	// TODO bookmaker review
	$(document).on('click', 'table.payment__table .payment__brand-td', function () {
		let $parent = $(this).parents('tr');
		$parent.attr('data-expanded', function(index, attr) {
			return (typeof attr == 'undefined' || attr === 'false' || !attr);
		});
	});

	$(document).on('click', '[data-action]', function(event) {
		//Марат сказал добавить эту проверку
		if(! $(event.target).closest('a:not([data-action="slide-toggle"])').length)
		{
			if ($(this).hasClass('subslider') && event.target.nodeName === 'A') {
				return;
			}
			if ($(this).data('action') === 'slide-toggle' && event.target.nodeName === 'INPUT') {
				return;
			}
			if ($(this).data('action') === 'slide-toggle' && event.target.nodeName === 'BUTTON') {
				return;
			}

			event.preventDefault();
			if ($(this).hasClass('subslider')) {
				let isDesktop = window.getComputedStyle($(this).find('td:first')[0], ':after').getPropertyValue('content') == 'none';
				if (isDesktop) {
					return false;
				}
			}
			let action = $(this).data('action'),
				expanded = $(this).attr('data-expanded'),
				target = $(this).data('target'),
				trigger = $(this).data('trigger');
			if (trigger !== undefined) {
				let triggerElem = $(this).find(trigger);

				// console.log(triggerElem, event.target);
				if (!triggerElem.is(event.target) && triggerElem.has(event.target).length === 0) {
					return false;
				}
			}

			switch (action) {
				case 'slide-toggle':
					$(target).toggle();
					break;
				case 'show-file-input':
					$(target).trigger('click');
					break;
				// Переключение табов
				case 'select-tab':
					$(this).siblings().removeClass('active');
					$(this).addClass('active');
					$(target).siblings().removeClass('active');
					$(target).addClass('active');
					break;
			}
			if (expanded !== undefined) {
				$(this).attr('data-expanded', expanded === 'false');
			}
		}
	});
	
	  // Счетчик символов
	$(document).on('keyup', 'textarea[data-counter]', function() {
		let maxlength = $(this).data('limit'),
			counter = $(this).data('counter'),
		  	length = $(this).val().length;
	
		if($(counter).length) {
			$(counter).find('.form__counter-number').text(`${length} / ${maxlength}`);
		}
	});

	// TODO в индекс бандл
	if (typeof bodymovin !== 'undefined') {
		var animation = bodymovin.loadAnimation({
			container: document.getElementById('lottie'), // Required
			path: 'animation.json',
			renderer: 'canvas',
			loop: true,
			autoplay: true,
		});
	}

	autosize($('textarea[data-autosize="true"]'));

	// $('.reviews-list').slick({
	// 	slidesToShow: 4,
	// 	slidesToScroll: 1,
	// 	dots: false,
	// 	infinite: false,
	// 	//- prevArrow: '<div class="slider-arrow-gr prev"><span class="icon icon-arrow slider-arrow prev"></span></div>',
	// 	prevArrow: '',
	// 	nextArrow: '<div class="slider-arrow-gr next"><span class="icon icon-arrow slider-arrow next"></span></div>',
	// 	swipe: false,
	// 	responsive: [
	// 		{
	// 			breakpoint: 1200,
	// 			settings: {
	// 				slidesToShow: 3,
	// 			}
	// 		},
	// 		{
	// 			breakpoint: 768,
	// 			settings: {
	// 				variableWidth: true,
	// 				slidesToShow: 1,
	// 				arrows: false,
	// 				swipe: true,
	// 			}
	// 		},
	// 	]
	// });


	if(window.matchMedia("(min-width: 768px)").matches) {
		if ($('.top-slider').length !== 0) {
			$('.top-slider').slick({
				slidesToShow: 4,
				slidesToScroll: 1,
				dots: false,
				infinite: false,
				prevArrow: '<div class="slider-arrow-gr prev"><span class="icon icon-arrow slider-arrow prev"></span></div>',
				nextArrow: '<div class="slider-arrow-gr next"><span class="icon icon-arrow slider-arrow next"></span></div>',
				swipe: false,
				adaptiveHeight: false,
				responsive: [
					{
						breakpoint: 1200,
						settings: {
							slidesToShow: 3,
						}
					},
					{
						breakpoint: 900,
						settings: {
							slidesToShow: 2,
						}
					},
				]
			});
		}
	}

	$(document).on("click", 'a[disabled]', function(e) {
		e.preventDefault();
		return false;
	});

	if(window.matchMedia("(min-width: 992px)").matches) {
		let initCustomScrollControls = function (elements) {
			elements.each(function(index, element) {
				// Проверяем нужны ли вообще стрелки
				if($(element)[0].scrollWidth > $(element)[0].offsetWidth) {
					$(element).parents('.custom-scroll-wrap').addClass('overflowed-right');
					$(element).after('<div class="slider-arrow-gr prev"><span class="icon icon-arrow slider-arrow prev"></span></div>');
					$(element).after('<div class="slider-arrow-gr next"><span class="icon icon-arrow slider-arrow next"></span></div>');
				}
			});
		};
		initCustomScrollControls($('.custom-scroll'));
		$(document).on('shown.bs.bs-modal', '.bs-modal', function () {
			let $customScroll = $(this).find('.custom-scroll');
			if ($customScroll.length !== 0) {
				initCustomScrollControls($customScroll);
			}
		});
		// TODO review form
		$(document).on('fileuploaddone', function (e, data) {
			if (data.result.failure) {
				return;
			}
			let $customScroll = $('.custom-scroll');
			if ($customScroll.length !== 0) {
				initCustomScrollControls($customScroll);
			}
		});

		$(document).on('click', '.custom-scroll-wrap .slider-arrow', function() {
			let scrollBlock = $(this).parent().siblings('.custom-scroll');
			let scrollParent = $(this).parents('.custom-scroll-wrap');
			let width = scrollParent.data('scroll');

			// двигаем скролл
			scrollBlock.animate({
				scrollLeft: $(this).hasClass('prev') ? `-=${width}` : `+=${width}`
			}, 300, 'swing');

			if($(this).hasClass('next')) {
				// так как проскроллили вправо, логично поставить стрелку на скролл влево
				scrollParent.addClass('overflowed-left');

				// Проверяем, если скроллить вправо некуда, то убираем стрелку
				if(scrollBlock[0].scrollWidth - scrollBlock[0].scrollLeft - scrollBlock[0].clientWidth - width < 1) {
					scrollParent.removeClass('overflowed-right');
				}
			}

			if($(this).hasClass('prev')) {
				// так как проскроллили влево, логично поставить стрелку на скролл вправо
				scrollParent.addClass('overflowed-right');

				// Проверяем, если скроллить влево некуда, то убираем стрелку
				if((scrollBlock[0].scrollLeft - 300) <= 0) {
					scrollParent.removeClass('overflowed-left');
				}
			}
		});
	}
});
