import './bootstrap';

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

const calendarElements = document.querySelectorAll('.js-booking-calendar');

calendarElements.forEach((element) => {
	const eventsUrl = element.getAttribute('data-events-url');

	const calendar = new Calendar(element, {
		plugins: [dayGridPlugin],
		initialView: 'dayGridMonth',
		height: 'auto',
		firstDay: 1,
		events: {
			url: eventsUrl,
			method: 'GET',
		},
		headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: '',
		},
		eventTimeFormat: {
			hour: '2-digit',
			minute: '2-digit',
			hour12: false,
		},
	});

	calendar.render();
});

// ── Mobile navigation toggle ─────────────────────────────────────────────────
// Wait until the DOM is ready before attaching event listeners.
document.addEventListener('DOMContentLoaded', function () {
	// All mobile menu toggle buttons (guest + authenticated nav).
	const toggles = document.querySelectorAll('.js-nav-toggle');

	toggles.forEach(function (toggle) {
		toggle.addEventListener('click', function () {
			// Read which menu this button controls.
			const targetId = toggle.getAttribute('data-target');
			const menu = document.getElementById(targetId);

			// Safety guard: do nothing if target menu is missing.
			if (!menu) {
				return;
			}

			// Toggle menu visibility on mobile.
			const isOpen = menu.classList.toggle('is-open');

			// Keep accessibility state in sync for screen readers.
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});
	});

	// When resizing to desktop, force mobile menus closed and reset state.
	window.addEventListener('resize', function () {
		if (window.innerWidth > 768) {
			document.querySelectorAll('.nav-buttons').forEach(function (menu) {
				menu.classList.remove('is-open');
			});

			// Reset ARIA expanded state on all toggle buttons.
			toggles.forEach(function (toggle) {
				toggle.setAttribute('aria-expanded', 'false');
			});
		}
	});
});
