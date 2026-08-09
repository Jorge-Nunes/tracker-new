import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'list',
    singular: 'Share',
    client: '$tarkan',
    api: { load: 'getShares', create: 'createShare', update: 'updateShare', remove: 'deleteShare' },
    mut: { set: 'set', add: 'add', update: 'update', remove: 'delete' },
    act: { load: 'load', save: 'save', remove: 'delete' },
    silentLoad: true,
    getters: {
        getShare(state) {
            return (id) => state.list.find((f) => f.id === id);
        },
    },
});
