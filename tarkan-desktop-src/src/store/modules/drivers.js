import createCrudModule from './factory';

export default createCrudModule({
    listKey: 'driverList',
    singular: 'Driver',
    api: { load: 'getDrivers', create: 'createDriver', update: 'updateDriver', remove: 'deleteDriver' },
    mut: { set: 'setDrivers', add: 'addDrivers', update: 'updateDrivers', remove: 'deleteDriver' },
    act: { load: 'load', save: 'save', remove: 'deleteDriver' },
    getters: {
        getDriver(state) {
            return (id) => state.driverList.find((u) => u.id === id);
        },
        getDriverByUniqueId(state) {
            return (id) => state.driverList.find((u) => u.uniqueId === id);
        },
    },
});
