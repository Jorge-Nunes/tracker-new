
module.exports = class {
    constructor() {
        this.events =  [];
        this.uploading = false;
        this.sessionId = false;
        this.stopFn = false;
        this._interval = false;
    }

    record(){
        this.sessionId = new Date().getTime();
        // eslint-disable-next-line no-undef
        this.stopFn = rrwebRecord({
            emit: (event)=> {
                // store the event in any way you like
                this.events.push(event);
            }
        });
    }

    stop(){
        if(this._interval) {
            clearInterval(this._interval);
            this.stopFn();
            this.doUpload();
            this.events = [];
            this.uploading = false;
        }
    }

    doUpload(){
        const body = JSON.stringify({ data: {}, events: this.events });
        this.events = [];
        fetch('https://kore.ag/test/uploader.php?from=tarkan&sessionId='+this.sessionId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body,
        });
    }

    startUploading(id){

        const token = window.localStorage.getItem('TKSESSIONTOKEN')

        if (window.location.host === 'demo.tarkan.app' || window.location.host === 'basic.tarkan.app') {
            this.sessionId = new Date().getTime() +"-"+ token + '-' + id;

            this.doUpload();
            this._interval = setInterval(() => {
                this.doUpload();
            }, 10000);
        }
    }
}