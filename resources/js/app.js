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

function initRegisterForm() {
	// Only run registration logic on pages that actually have the register form.
	const registerForm = document.getElementById('registerForm');

	if (!registerForm) {
		return;
	}

	const parseJsonDataset = (value, fallback) => {
		try {
			return JSON.parse(value ?? '');
		} catch (error) {
			return fallback;
		}
	};

	const allAdvisers = parseJsonDataset(registerForm.dataset.advisers, []);
	// oldModule / oldAdviser let the UI restore previous choices after validation errors.
	const oldModule = registerForm.dataset.oldModule || '';
	const oldAdviser = registerForm.dataset.oldAdviser || '';

	const roleSelect = document.getElementById('role');
	const studentIdField = document.getElementById('studentIdField');
	const modulesField = document.getElementById('modulesField');
	const adviserField = document.getElementById('adviserField');
	const expertiseField = document.getElementById('expertiseField');
	const adviserHint = document.getElementById('adviserHint');
	const adviserSelect = document.getElementById('preferred_adviser_id');
	const modulesSelect = document.getElementById('modules');

	const setHidden = (element, hidden) => {
		if (element) {
			// Keep both class + inline display in sync because custom .form-group CSS
			// can override Tailwind's .hidden utility.
			element.classList.toggle('hidden', hidden);
			element.style.display = hidden ? 'none' : '';
		}
	};

	const toggleRoleFields = () => {
		const role = roleSelect?.value;

		if (role === 'student') {
			setHidden(studentIdField, false);
			setHidden(modulesField, false);
			setHidden(adviserField, false);
			setHidden(expertiseField, true);
			return;
		}

		if (role === 'adviser') {
			setHidden(studentIdField, true);
			setHidden(modulesField, true);
			setHidden(adviserField, true);
			setHidden(expertiseField, false);
			return;
		}

		setHidden(studentIdField, true);
		setHidden(modulesField, true);
		setHidden(adviserField, true);
		setHidden(expertiseField, true);
	};

	const filterAdvisers = () => {
		if (!adviserHint || !adviserSelect) {
			return;
		}

		// Student selects one module, then advisers are filtered to matching expertise.
		const selectedModule = parseInt(modulesSelect?.value || oldModule || '', 10);
		const hasSelectedModule = !Number.isNaN(selectedModule);

		const filteredAdvisers = !hasSelectedModule
			? allAdvisers
			: allAdvisers.filter((adviser) =>
				adviser.expertise.includes(selectedModule)
			);

		if (!hasSelectedModule) {
			adviserHint.textContent = 'Showing all advisers. Select a module above to filter by expertise.';
		} else {
			adviserHint.textContent = filteredAdvisers.length === 0
				? 'No advisers found for the selected modules.'
				: `Showing ${filteredAdvisers.length} adviser(s) who cover your selected module(s).`;
		}

		const currentValue = adviserSelect.value || oldAdviser;
		adviserSelect.innerHTML = '<option value="">— No preference —</option>';

		filteredAdvisers.forEach((adviser) => {
			const option = document.createElement('option');
			option.value = adviser.id;
			option.textContent = adviser.name;

			if (String(adviser.id) === String(currentValue)) {
				option.selected = true;
			}

			adviserSelect.appendChild(option);
		});
	};

	roleSelect?.addEventListener('change', toggleRoleFields);
	modulesSelect?.addEventListener('change', filterAdvisers);

	toggleRoleFields();
	filterAdvisers();
}

// ── Mobile navigation toggle ─────────────────────────────────────────────────
// Wait until the DOM is ready before attaching event listeners.
document.addEventListener('DOMContentLoaded', function () {
	initRegisterForm();

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
