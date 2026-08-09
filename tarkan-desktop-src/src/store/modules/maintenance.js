import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'list',
    singular: 'Maintenance',
    api: { load: 'getMaintenance', create: 'createMaintenance', update: 'updateMaintenance', remove: 'deleteMaintenance' },
    mut: { set: 'set', add: 'add', update: 'update', remove: 'remove' },
    act: { load: 'load', save: 'save', remove: 'delete' },
    guardRemove: true,
});
