
import connector from './tarkanConnector.js';

    /**
     * Install plugin
     * @param app
     * @param axios
     */


    function tarkan(app,server) {



        if (tarkan.installed) {
            return;
        }

        if (!server) {
            console.error('SERVER is required');
            return;
        }

        tarkan.installed = true;


        let tarkanConnector = new connector(server,app);

        
         app.config.globalProperties.tarkan = tarkanConnector;
         app.config.globalProperties.$tarkan = tarkanConnector;
         window.$tarkan = tarkanConnector;

         app.provide('tarkan',tarkanConnector);


    }


    export default tarkan;
