
import connector from './traccarConnector.js';

    /**
     * Install plugin
     * @param app
     * @param axios
     */


    function traccar(app,server) {



        if (traccar.installed) {
            return;
        }

        if (!server) {
            console.error('SERVER is required');
            return;
        }

        traccar.installed = true;


        let traccarConnector = new connector(server,app);

        
         app.config.globalProperties.traccar = traccarConnector;
         app.config.globalProperties.$traccar = traccarConnector;
         window.$traccar = traccarConnector;

         app.provide('traccar',traccarConnector);


    }


    export default traccar;
