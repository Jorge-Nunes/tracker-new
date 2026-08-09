/**
 * Factory de módulos vuex CRUD padronizados.
 *
 * Elimina a duplicação de state/getters/mutations/actions presente nos módulos
 * simples (grupos, calendários, drivers, usuários, manutenção, atributos,
 * comandos e shares).
 *
 * Config:
 *  - listKey:     chave do state (ex.: 'groupList')
 *  - singular:    nome no singular para o getter auto-gerado get<Singular>ById
 *  - api:         nomes dos métodos do connector: { load, create, update, remove }
 *  - mut:         nomes das mutations: { set, add, update, remove }
 *  - act:         nomes das actions: { load, save, remove }
 *  - getters:     getters extras (sobrescrevem o auto-gerado)
 *  - client:      global do connector ('$traccar' ou '$tarkan')
 *  - guardRemove: se true, rejeita remoção com id <= 0
 *  - silentLoad:  se true, load resolve mesmo em caso de erro
 *  - afterSave:   hook (ctx, data, isUpdate) executado após o save
 */
export default function createCrudModule(config) {
    const {
        listKey,
        singular,
        api,
        mut,
        act,
        getters = {},
        client = '$traccar',
        guardRemove = false,
        silentLoad = false,
        afterSave = null,
    } = config;

    const conn = () => window[client];

    return {
        namespaced: true,
        state: () => ({ [listKey]: [] }),
        getters: {
            [`get${singular}ById`]: (state) => (id) => state[listKey].find((x) => x.id === id),
            ...getters,
        },
        mutations: {
            [mut.set](state, value) {
                state[listKey] = value;
            },
            [mut.add](state, value) {
                state[listKey].push(value);
            },
            [mut.update](state, value) {
                const index = state[listKey].findIndex((x) => x.id === value.id);
                if (index > -1) {
                    state[listKey].splice(index, 1, value);
                }
            },
            [mut.remove](state, value) {
                const index = state[listKey].findIndex((x) => x.id === value);
                if (index > -1) {
                    state[listKey].splice(index, 1);
                }
            },
        },
        actions: {
            [act.load]({ commit }) {
                const request = conn()[api.load]().then(({ data }) => {
                    commit(mut.set, data);
                });

                return silentLoad ? request.catch(() => {}) : request;
            },
            [act.save]({ commit, rootState }, params) {
                const isUpdate = !!params.id;
                const request = isUpdate
                    ? conn()[api.update](params.id, params)
                    : conn()[api.create](params);

                return request.then(({ data }) => {
                    commit(isUpdate ? mut.update : mut.add, data);
                    if (afterSave) {
                        afterSave({ commit, rootState }, data, isUpdate);
                    }
                    return data;
                });
            },
            [act.remove]({ commit }, id) {
                if (guardRemove && id <= 0) {
                    return Promise.reject();
                }

                return conn()[api.remove](id).then(() => {
                    commit(mut.remove, id);
                });
            },
        },
    };
}
