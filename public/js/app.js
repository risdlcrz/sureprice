// Main application JavaScript file
import './bootstrap';

// FullCalendar imports
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import interactionPlugin from '@fullcalendar/interaction';

// Frappe Gantt
import Gantt from 'frappe-gantt';

// Tippy.js and Popper.js
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';

// Expose Calendar and Gantt globally if needed in Blade files (alternative to direct CDN use)
window.FullCalendar = { Calendar, dayGridPlugin, timeGridPlugin, resourceTimelinePlugin, interactionPlugin };
window.Gantt = Gantt;
window.tippy = tippy;

// Add any global JavaScript functionality here 

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const key = window.PUSHER_APP_KEY || '';
const cluster = window.PUSHER_APP_CLUSTER || '';

if (key && cluster) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: key,
        cluster: cluster,
        forceTLS: true
    });
} else {
    window.Echo = null; // fallback: no real-time
} 