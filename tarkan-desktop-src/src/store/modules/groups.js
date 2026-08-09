import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'groupList',
    singular: 'Group',
    api: { load: 'getGroups', create: 'createGroup', update: 'updateGroup', remove: 'deleteGroup' },
    mut: { set: 'setGroups', add: 'addGroup', update: 'updateGroup', remove: 'removeGroup' },
    act: { load: 'load', save: 'save', remove: 'delete' },
    guardRemove: true,
    getters: {
        getGroup(state) {
            return (id) => state.groupList.find((g) => g.id === id);
        },
        getGroupNameById(state) {
            return (id) => {
                const group = state.groupList.find((g) => g.id === id);
                return group ? group.name : '--';
            };
        },
    },
});
