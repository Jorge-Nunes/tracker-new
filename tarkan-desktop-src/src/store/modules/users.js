import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'userList',
    singular: 'User',
    api: { load: 'getUsers', create: 'createUser', update: 'updateUser', remove: 'deleteUser' },
    mut: { set: 'setUsers', add: 'addUser', update: 'updateUser', remove: 'deleteUser' },
    act: { load: 'load', save: 'save', remove: 'deleteUser' },
    getters: {
        getUser(state) {
            return (id) => state.userList.find((u) => u.id === id);
        },
        getUsers(state) {
            return state.userList.filter((u) => {
                if (u.attributes['isShared'] && u.attributes['isShared'] !== null) {
                    return false;
                }
                return true;
            });
        },
    },
    afterSave({ rootState, commit }, data, isUpdate) {
        if (isUpdate && rootState.auth.id === data.id) {
            commit('setAuth', data, { root: true });
        }
    },
});
