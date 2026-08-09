import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'commandList',
    singular: 'Command',
    api: { load: 'getSavedCommands', create: 'createSavedCommand', update: 'updateSavedCommand', remove: 'deleteSavedCommand' },
    mut: { set: 'setCommands', add: 'addCommand', update: 'updateCommand', remove: 'removeCommand' },
    act: { load: 'load', save: 'save', remove: 'delete' },
});
