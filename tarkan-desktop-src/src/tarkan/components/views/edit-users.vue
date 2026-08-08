<script setup>
import 'element-plus/es/components/input/style/css';
import 'element-plus/es/components/button/style/css';
import 'element-plus/es/components/switch/style/css';
import 'element-plus/es/components/select/style/css';
import 'element-plus/es/components/option/style/css';
import 'element-plus/es/components/dialog/style/css';
import 'element-plus/es/components/tab-pane/style/css';
import 'element-plus/es/components/tabs/style/css';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/checkbox/style/css';

import { ElDialog, ElMessage, ElButton, ElInput } from 'element-plus';

import { ref, defineExpose, inject, computed, watch } from 'vue';
import { useStore } from 'vuex';

import { ElMessageBox } from 'element-plus/es/components/message-box';
import { ElNotification } from 'element-plus/es/components/notification';
import Paginate from '../../../components/base/Paginate.vue';

const showTip = (evt, text) => {
    window.$showTip(evt, text);
};

const hideTip = (evt, text) => {
    window.$hideTip(evt, text);
};

const store = useStore();
const query = ref('');
const selected = ref(0);
const show = ref(false);
const currentPage = ref(1);

const editUserRef = inject('edit-user');
const linkObjectsRef = inject('link-objects');
const logObjectsRef = inject('log-objects');

const sorting = ref('id-asc');

const toggleSorting = (s) => {
    const p = sorting.value.split('-');

    if (p[0] === s) {
        sorting.value = s + '-' + (p[1] === 'asc' ? 'desc' : 'asc');
    } else {
        sorting.value = s + '-asc';
    }
};

const filteredUsers = computed(() => {
    const p = sorting.value.split('-');

    return [...store.getters['users/getUsers']]
        .filter((f) => {
            for (let k of Object.keys(f)) {
                if (String(f[k]).toLowerCase().match(query.value.toLowerCase())) {
                    return true;
                }
            }

            for (let k of Object.keys(f.attributes)) {
                if (String(f.attributes[k]).toLowerCase().match(query.value.toLowerCase())) {
                    return true;
                }
            }

            return false;
            //return String(f.name).toLowerCase().match(query.value.toLowerCase()) || String(f.email).toLowerCase().match(query.value.toLowerCase());
        })
        .sort((a, b) => {
            if (p[0] === 'administrator' || p[0] === 'disabled') {
                if (p[1] === 'asc') {
                    return a[p[0]] === true && b[p[0]] === false ? 1 : -1;
                } else {
                    return a[p[0]] === true && b[p[0]] === false ? -1 : 1;
                }
            } else if (p[0] === 'name') {
                if (p[1] === 'asc') {
                    return a[p[0]].localeCompare(b[p[0]]);
                } else {
                    const t = a[p[0]].localeCompare(b[p[0]]);
                    return t === 1 ? -1 : t === -1 ? 1 : 0;
                }
            } else if (!a[p[0]] || !b[p[0]]) {
                return p[1] === 'desc' ? 1 : -1;
            } else if (p[0] === 'created_at') {
                if (new Date(a[p[0]]) < new Date(b[p[0]])) {
                    return p[1] === 'asc' ? 1 : -1;
                } else if (new Date(a[p[0]]) > new Date(b[p[0]])) {
                    return p[1] === 'desc' ? 1 : -1;
                } else {
                    return 0;
                }
            } else if (a[p[0]] > b[p[0]]) {
                return p[1] === 'asc' ? 1 : -1;
            } else if (a[p[0]] < b[p[0]]) {
                return p[1] === 'desc' ? 1 : -1;
            } else {
                return 0;
            }
        });
});

watch(filteredUsers, () => {
    currentPage.value = 1;
});

const doDelete = () => {
    if (selected.value === 0) {
        ElMessage.error('Você precisa selecionar um usuário');
        return false;
    }

    if (selected.value === store.state.auth.id) {
        ElMessage.error('Você não pode se deletar!');
        return false;
    }

    const user = store.getters['users/getUser'](selected.value);

    if (user.id < store.state.auth.id && user.administrator) {
        ElMessage.error('Você não pode deletar um admin superior a você!');
        return false;
    }

    ElMessageBox.confirm('Você está excluindo o usuário "' + user.name + '", deseja continuar?', 'Perigo!', {
        confirmButtonText: 'Excluir',
        confirmButtonClass: 'danger',
        cancelButtonText: 'Cancelar',
        type: 'warning',
    })
        .then(() => {
            store
                .dispatch('users/deleteUser', selected.value)
                .then(() => {
                    ElNotification({
                        title: 'Successo',
                        message: 'Usuário deletado com sucesso.',
                        type: 'success',
                    });
                    selected.value = 0;
                })
                .catch((e) => {
                    ElNotification({
                        title: 'Erro',
                        message: e.response.data,
                        type: 'danger',
                    });
                });
        })
        .catch(() => {
            ElMessage.error('Ação cancelada pelo usuário');
        });
};

const showUsers = () => {
    show.value = true;
};

defineExpose({
    showUsers,
});
</script>

<template>
    <el-dialog :lock-scroll="true" :top="'50px'" :width="'60%'" v-model="show" title="Teste">
        <template v-slot:title>
            <div style="border-bottom: #e0e0e0 1px solid; padding: 20px">
                <div class="modal-title" style="display: flex; width: calc(100% - 50px)">
                    <el-input v-model="query" :placeholder="KT('user.search')" style="--el-input-border-radius: 5px; margin-right: 5px"></el-input>

                    <el-button
                        v-if="store.getters.advancedPermissions(17)"
                        @mouseleave="hideTip"
                        @mouseenter.stop="showTip($event, KT('user.add'))"
                        type="primary"
                        @click="editUserRef.newUser()"
                        ><i class="fas fa-user-plus"></i
                    ></el-button>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <div style="border-top: #e0e0e0 1px solid; padding: 20px; display: flex; justify-content: flex-start">
                <el-button
                    v-if="store.getters.advancedPermissions(19)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('user.remove'))"
                    type="danger"
                    :plain="selected === 0"
                    @click="doDelete()">
                    <i class="fas fa-user-minus"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('user.edit'))"
                    type="warning"
                    :plain="selected === 0"
                    @click="editUserRef.editUser(selected)">
                    <i class="fas fa-user-edit"></i>
                </el-button>

                <el-button
                    v-if="store.state.server.isPlus && store.state.auth.administrator"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('user.logs'))"
                    plain
                    :disabled="selected === 0"
                    @click="logObjectsRef.showLogs({ userId: selected })">
                    <i class="fas fa-clipboard-list"></i>
                </el-button>

                <div style="margin-left: 5px; margin-right: 5px" :set="(user = store.getters['users/getUser'](selected))">
                    <el-button
                        v-if="store.getters.advancedPermissions(16) && store.getters.advancedPermissions(18)"
                        @mouseleave="hideTip"
                        @mouseenter.stop="showTip($event, KT('user.users'))"
                        plain
                        :disabled="selected === 0 || !(user && (user.userLimit === -1 || user.userLimit > 0))"
                        @click="linkObjectsRef.showObjects({ userId: selected, type: 'users' })">
                        <i class="fas fa-users"></i>
                    </el-button>
                </div>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(8)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('device.devices'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'devices' })">
                    <i class="fas fa-car"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(40)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('geofence.geofences'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'geofences' })">
                    <i class="fas fa-draw-polygon"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(48)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('group.groups'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'groups' })">
                    <i class="far fa-object-group"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(32)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('notification.notifications'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'notifications' })">
                    <i class="far fa-envelope"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(88)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('calendar.calendars'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'calendars' })">
                    <i class="far fa-calendar-alt"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(64)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('attribute.computedAttributes'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'attributes' })">
                    <i class="fas fa-server"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(80)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('driver.drivers'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'drivers' })">
                    <i class="fas fa-user-tag"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(56)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('command.savedCommands'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'commands' })">
                    <i class="far fa-keyboard"></i>
                </el-button>

                <el-button
                    v-if="store.getters.advancedPermissions(18) && store.getters.advancedPermissions(96)"
                    @mouseleave="hideTip"
                    @mouseenter.stop="showTip($event, KT('maintenance.maintenance'))"
                    plain
                    :disabled="selected === 0"
                    @click="linkObjectsRef.showObjects({ userId: selected, type: 'maintence' })">
                    <i class="fas fa-tools"></i>
                </el-button>
            </div>
        </template>

        <div class="itm" style="display: flex; background: #eeeeee">
            <div @click="toggleSorting('id')" style="padding: 10px; width: 30px; font-size: 12px">
                {{ KT('user.id') }}

                <span v-if="sorting === 'id-asc'">
                    <i class="fas fa-sort-alpha-down"></i>
                </span>
                <span v-else-if="sorting === 'id-desc'">
                    <i class="fas fa-sort-alpha-up"></i>
                </span>
                <span v-else>
                    <i style="color: silver" class="fas fa-sort-alpha-down"></i>
                </span>
            </div>

            <div @click="toggleSorting('name')" style="flex: 1; padding: 10px; font-size: 12px">
                {{ KT('user.name') }}

                <span v-if="sorting === 'name-asc'">
                    <i class="fas fa-sort-alpha-down"></i>
                </span>
                <span v-else-if="sorting === 'name-desc'">
                    <i class="fas fa-sort-alpha-up"></i>
                </span>
                <span v-else>
                    <i style="color: silver" class="fas fa-sort-alpha-down"></i>
                </span>
            </div>
            <div @click="toggleSorting('email')" style="flex: 1; padding: 10px; font-size: 12px; text-align: center">
                {{ KT('user.email') }}

                <span v-if="sorting === 'email-asc'">
                    <i class="fas fa-sort-alpha-down"></i>
                </span>
                <span v-else-if="sorting === 'email-desc'">
                    <i class="fas fa-sort-alpha-up"></i>
                </span>
                <span v-else>
                    <i style="color: silver" class="fas fa-sort-alpha-down"></i>
                </span>
            </div>
            <div @click="toggleSorting('administrator')" style="width: 90px; padding: 10px; font-size: 12px; text-align: center">
                {{ KT('user.admin') }}

                <span v-if="sorting === 'administrator-asc'">
                    <i class="fas fa-sort-alpha-down"></i>
                </span>
                <span v-else-if="sorting === 'administrator-desc'">
                    <i class="fas fa-sort-alpha-up"></i>
                </span>
                <span v-else>
                    <i style="color: silver" class="fas fa-sort-alpha-down"></i>
                </span>
            </div>
            <div @click="toggleSorting('disabled')" style="width: 92px; padding: 10px; font-size: 12px; text-align: center">
                {{ KT('user.disabled') }}

                <span v-if="sorting === 'disabled-asc'">
                    <i class="fas fa-sort-alpha-down"></i>
                </span>
                <span v-else-if="sorting === 'disabled-desc'">
                    <i class="fas fa-sort-alpha-up"></i>
                </span>
                <span v-else>
                    <i style="color: silver" class="fas fa-sort-alpha-down"></i>
                </span>
            </div>
        </div>
        <div style="height: calc(100vh - 300px); overflow: hidden; overflow-y: scroll">
            <!-- Lista de usuários -->
            <paginate :items="filteredUsers" :per-page="10" v-model="currentPage">
                <template v-slot="{item, index}">
                    <div
                        class="itm"
                        @click="selected = item.id"
                        :class="{ tr1: index % 2, tr2: !(index % 2), selected: selected === item.id }"
                        style="display: flex">
                        <div style="width: 30px; padding: 10px; font-size: 14px">{{ item.id }}</div>
                        <div style="flex: 1; padding: 10px; font-size: 14px">{{ item.name }}</div>
                        <div style="flex: 1; padding: 10px; font-size: 14px; text-align: center">{{ item.email }}</div>
                        <div style="width: 90px; padding: 10px; font-size: 14px; text-align: center">{{ item.administrator ? $t('yes') : $t('no') }}</div>
                        <div style="width: 90px; padding: 10px; font-size: 14px; text-align: center">{{ item.disabled ? $t('yes') : $t('no') }}</div>
                    </div>
                </template>
            </paginate>
            <!-- Fim lista de usuários -->
        </div>
    </el-dialog>
</template>

<style>
.itm {
    border-bottom: silver 1px dotted;
}

.itm div {
    border-right: silver 1px dotted;
}

.tr1 {
    background: #f3f3f3;
}

.tr2 {
    background: #f8f8f8;
}

.selected {
    background: rgba(5, 167, 227, 0.05) !important;
}

.itm div:last-child {
    border-right: none;
}

.el-select.el-select--large {
    width: 100%;
}

.el-dialog__footer {
    margin-top: 0px;
}

.el-tabs__nav-wrap {
    padding-left: 20px;
    padding-right: 20px;
}

.el-tabs__content {
    padding-left: 20px;
    padding-right: 20px;
}

.danger {
    --el-button-text-color: var(--el-color-danger) !important;
    --el-button-bg-color: #fef0f0 !important;
    --el-button-border-color: #fbc4c4 !important;
    --el-button-hover-text-color: var(--el-color-white) !important;
    --el-button-hover-bg-color: var(--el-color-danger) !important;
    --el-button-hover-border-color: var(--el-color-danger) !important;
    --el-button-active-text-color: var(--el-color-white) !important;
    --el-button-active-border-color: var(--el-color-danger) !important;
}
</style>