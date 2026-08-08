
import {ElMessage} from "element-plus/es/components/message";
import {ElMessageBox} from "element-plus/es/components/message-box";
import {ElNotification} from "element-plus/es/components/notification";
import store from '../../store/'


const actAnchor = async (id)=>{


    const device = store.getters['devices/getDevice'](id)
    const isAnchored = store.getters['geofences/isAnchored'](id)
    const position = store.getters['devices/getPosition'](id);


    if(isAnchored){

        ElMessageBox.confirm(
            'Deseja realmente desativar a âncora para o veiculo "' + device.name + '"?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar',
                type: 'warning',
            }
        ).then(() => {

            console.log(isAnchored);

            store.dispatch("geofences/delete", isAnchored.id).then(() => {
                ElNotification({
                    title: 'Successo',
                    message: 'Função âncora foi desativada com sucesso.',
                    type: 'success',
                });
            });


        }).catch(() => {
            ElMessage.error('Ação cancelada pelo usuário');
        })

    }else {

        ElMessageBox.confirm(
            'Deseja realmente ativar a âncora para o veiculo "' + device.name + '"?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar',
                type: 'warning',
            }
        ).then(() => {

            const params = {
                "id": -1,
                "name": "Ancora " + device.name,
                "description": "",
                "area": "CIRCLE (" + position.latitude + " " + position.longitude + ", 150)",
                "calendarId": 0,
                "attributes": {
                    "deviceId": device.id,
                    "isAnchor": true
                }
            }


            store.dispatch("geofences/save", params).then((fence) => {
                window.$traccar.linkObjects({deviceId: device.id, geofenceId: fence.id}).then(() => {
                    ElNotification({
                        title: 'Successo',
                        message: 'Função âncora foi ativada com sucesso.',
                        type: 'success',
                    });

                });
            });


        }).catch(() => {
            ElMessage.error('Ação cancelada pelo usuário');
        })
    }
}

export default actAnchor;