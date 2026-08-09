import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'calendarList',
    singular: 'Calendar',
    api: { load: 'getCalendars', create: 'createCalendar', update: 'updateCalendar', remove: 'deleteCalendar' },
    mut: { set: 'setCalendars', add: 'addCalendar', update: 'updateCalendar', remove: 'deleteCalendar' },
    act: { load: 'load', save: 'save', remove: 'deleteCalendar' },
});
