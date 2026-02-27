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
