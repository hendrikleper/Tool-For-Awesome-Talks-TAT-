$(document).ready(function(){
	var $el = $('.thumbnail-sidebar .slide-list > li');
	var $themeStylesheet = $('#theme-stylesheet');
	var $themeToggle = $('.theme-toggle');
	var $infoButton = $('.info-button');
	var $infoPopover = $('#info-popover');
	var themeStorageKey = 'tat-theme';

	function setTheme(theme) {
		var isDark = theme === 'dark';
		$themeStylesheet.attr('href', 'css/' + (isDark ? 'dark' : 'light') + '-template.css');
		$themeToggle
			.attr('aria-pressed', isDark ? 'true' : 'false')
			.text(isDark ? '☀️' : '🌚');
	}

	var savedTheme = localStorage.getItem(themeStorageKey);
	setTheme(savedTheme === 'light' ? 'light' : 'dark');

	$themeToggle.on('click', function(){
		var nextTheme = $themeStylesheet.attr('href').indexOf('dark-template.css') >= 0 ? 'light' : 'dark';
		localStorage.setItem(themeStorageKey, nextTheme);
		setTheme(nextTheme);
	});

	function closeInfoPopover() {
		$infoPopover.prop('hidden', true);
		$infoButton.attr('aria-expanded', 'false');
	}

	$infoButton.on('click', function(){
		var isOpen = !$infoPopover.prop('hidden');
		$infoPopover.prop('hidden', isOpen);
		$infoButton.attr('aria-expanded', isOpen ? 'false' : 'true');
	});

	$(document).on('click', function(event){
		if (!$(event.target).closest('.info-control').length) {
			closeInfoPopover();
		}
	});

	$(document).on('keydown', function(event){
		if (event.key === 'Escape' && !$infoPopover.prop('hidden')) {
			closeInfoPopover();
			$infoButton.trigger('focus');
		}
	});

	$el.click(function(){
		previewSlide($(this));
	});

	$(document).keydown(function(e) {
		var $slides = $('.thumbnail-sidebar .slide-list > li');
		var activeIndex = $slides.filter('.active').index();
		var targetIndex;

		switch(e.key) {
			case 'ArrowLeft':
				targetIndex = activeIndex < 0 ? $slides.length - 1 : Math.max(0, activeIndex - 1);
				break;
			case 'ArrowRight':
				targetIndex = activeIndex < 0 ? 0 : Math.min($slides.length - 1, activeIndex + 1);
				break;
			case 'ArrowUp':
			case 'ArrowDown':
				if (activeIndex < 0) {
					targetIndex = e.key === 'ArrowUp' ? 0 : $slides.length - 1;
					break;
				}

				var rows = [];
				var rowTops = [];
				$slides.each(function(index) {
					var top = this.offsetTop;
					var rowIndex = rowTops.indexOf(top);
					if (rowIndex < 0) {
						rowIndex = rowTops.length;
						rowTops.push(top);
						rows.push([]);
					}
					rows[rowIndex].push(index);
				});

				var activeRow = rows.findIndex(function(row) {
					return row.indexOf(activeIndex) >= 0;
				});
				var column = rows[activeRow].indexOf(activeIndex);
				var rowOffset = e.key === 'ArrowUp' ? -1 : 1;
				var targetRow = (activeRow + rowOffset + rows.length) % rows.length;
				targetIndex = rows[targetRow][Math.min(column, rows[targetRow].length - 1)];
				break;
			default:
				return;
		}

		e.preventDefault();
		if ($slides.length) {
			previewSlide($slides.eq(targetIndex));
		}
	});

	if ($el.length) {
		previewSlide($el.first());
	}
});

function previewSlide($this) {
	if (!$this || !$this.length) {
		return;
	}

	var $preview = $('.preview .image-wrapper');
	var $figure = $this.find('figure');
	var imagePath = $figure.attr('data-image');

	$('.thumbnail-sidebar .slide-list > li').removeClass('active');
	$preview.empty();
	$('.preview').removeClass('text-slide image');

	if ($this.hasClass('image')) {
		$preview.append($('<img>', { src: imagePath, alt: '' }));
		if ($figure.attr('data-caption')) {
			$preview.append($('<div>', { class: 'caption' }).html($this.find('figcaption').html()));
			setTimeout(function(){
				var captionHeight = $('.caption').height();
				$('.preview.image img').css('height', ($(window).height() - 30 - (captionHeight * 2.5)) + 'px');
			}, 100);
		}
		$('.preview').addClass('image');
	} else if ($this.hasClass('text-slide')) {
		$preview.html($this.html());
		$('.preview').addClass('text-slide');
		$('.preview .image-wrapper a').attr('target', '_blank');
	} else if ($this.hasClass('video')) {
		$preview.append($('<video>', { controls: true, src: $this.attr('data-video') }));
	} else if ($this.hasClass('audio')) {
		$preview.append($('<audio>', { controls: true, src: $this.attr('data-audio') }));
	} else if ($this.hasClass('pdf')) {
		$preview.append($('<object>', {
			data: $this.attr('data-pdf'),
			type: 'application/pdf'
		}).append($('<embed>', {
			src: $this.attr('data-pdf'),
			type: 'application/pdf'
		})));
	}

	$this.addClass('active');
}
