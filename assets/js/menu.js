/*
 * Destinations + Tours mega menus (markup: header.php, styles: menu.css).
 *
 * Desktop opening is pure CSS (:hover / :focus-within). This adds:
 *  - region switching on hover / focus in the Destinations panel,
 *  - Escape to close, with focus returned to the menu link,
 *  - the accordion toggles used in the mobile drawer.
 */
(function () {
	var items = document.querySelectorAll('[data-mm]');
	if (!items.length) return;
	var desktop = window.matchMedia('(min-width: 1024px)');

	items.forEach(function (item) {
		var link = item.querySelector(':scope > a');
		var toggle = item.querySelector('.mm-toggle');
		var panel = item.querySelector('.mm-panel');

		// Region rail: show a region's destinations as soon as it is pointed
		// at or focused. Clicking still follows the link to the region page.
		var regions = item.querySelectorAll('[data-mm-region]');
		var activate = function (region) {
			regions.forEach(function (r) {
				var on = r === region;
				r.classList.toggle('is-active', on);
				r.setAttribute('aria-selected', String(on));
				var pane = document.getElementById(r.getAttribute('aria-controls'));
				if (pane) pane.classList.toggle('is-active', on);
			});
		};
		regions.forEach(function (r) {
			r.addEventListener('mouseenter', function () { if (desktop.matches) activate(r); });
			r.addEventListener('focus', function () { if (desktop.matches) activate(r); });
		});

		// Mobile accordion: the link keeps navigating; the chevron button opens.
		if (toggle) {
			toggle.addEventListener('click', function () {
				var open = !item.classList.contains('mm-open');
				items.forEach(function (other) {
					if (other !== item) {
						other.classList.remove('mm-open');
						var t = other.querySelector('.mm-toggle');
						if (t) t.setAttribute('aria-expanded', 'false');
					}
				});
				item.classList.toggle('mm-open', open);
				toggle.setAttribute('aria-expanded', String(open));
			});
		}

		// Escape closes the desktop panel even while hovered or focused.
		item.addEventListener('keydown', function (e) {
			if (e.key !== 'Escape' || !desktop.matches) return;
			item.classList.add('mm-closed');
			if (link) link.focus();
		});
		var reopen = function () { item.classList.remove('mm-closed'); };
		item.addEventListener('mouseleave', reopen);
		item.addEventListener('focusout', function (e) {
			if (!item.contains(e.relatedTarget)) reopen();
		});
		if (link) {
			link.addEventListener('keydown', function (e) {
				// Re-open with Down arrow after Escape, and step into the panel.
				if (e.key === 'ArrowDown' && desktop.matches && panel) {
					e.preventDefault();
					reopen();
					var first = panel.querySelector('a');
					if (first) first.focus();
				}
			});
		}
	});

	// Leaving mobile layout: collapse any open accordion.
	desktop.addEventListener('change', function () {
		items.forEach(function (item) {
			item.classList.remove('mm-open');
			var t = item.querySelector('.mm-toggle');
			if (t) t.setAttribute('aria-expanded', 'false');
		});
	});
})();
