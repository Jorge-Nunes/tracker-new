<script>

import {inject, onMounted, ref, provide} from "vue";
import {GLOBAL_LEAFLET_OPT, WINDOW_OR_GLOBAL} from "@vue-leaflet/vue-leaflet/src/utils";


export default{
  name: 'Kore-CanvaMarker',
  props: ['devices','zoom','map'],
  // eslint-disable-next-line no-unused-vars
  setup(props,context){



    let L = WINDOW_OR_GLOBAL.L;

    const leafletRef = ref([]);
    const useGlobalLeaflet = inject(GLOBAL_LEAFLET_OPT);

    const addLayer = inject("addLayer");
    //const options = {};

    const markerList = ref([]);

    const bases = {}
    const color1 = {}
    const color2 = {}
    const sizes = {};
    const radius = {};
    const cached = {};

    function loadModel(key,model,c1,c2,w,d=20) {
      return new Promise((resolve)=> {




        bases[key] = document.createElement('img');
        bases[key].src = '/img/cars/' + model + '_base.png';

        sizes[key] = w;
        radius[key] = d;

        bases[key].onload = ()=> {
          if (c1) {
            color1[key] = document.createElement('img');
            color1[key].src = '/img/cars/' + model + '_color1.png';
            color1[key].onload = ()=>{
              if (c2) {
                color2[key] = document.createElement('img');
                color2[key].src = '/img/cars/' + model + '_color2.png';
                color2[key].onload = ()=>{
                  resolve();
                }
              }else{
                resolve();
              }
            }
          }else{
            resolve();
          }
        }
      });
    }







    const imgCircle = document.createElement('img');
    imgCircle.src = '/img/circle.png';
    const imgContorno = document.createElement('img');
    imgContorno.src = '/img/contorno.png';


    function generateCachedModel(key,h1,s1,b1,h2,s2,b2){

        if(!bases[key]){
          return false;
        }
        var c = document.createElement('canvas');

        c.width = sizes[key];
        c.height = sizes[key];

        var ctx = c.getContext('2d');

        ctx.drawImage(bases[key], 0, 0, (sizes[key]), (sizes[key]));

        if(color1[key]) {
          ctx.filter = 'hue-rotate('+h1+'deg) saturate('+s1+') brightness('+b1+')';
          ctx.drawImage(color1[key], 0, 0, (sizes[key]), (sizes[key]));
        }

        if(color2[key]) {
          ctx.filter = 'hue-rotate('+h2+'deg) saturate('+s2+') brightness('+b2+')';
          ctx.drawImage(color2[key], 0, 0, (sizes[key]),(sizes[key]));
        }

        return c;
    }

    function getCachedModel(key,h1,s1,b1,h2,s2,b2){

      const cKey = key+'-'+h1+'-'+s1+'-'+b1+'-'+h2+'-'+s2+'-'+b2;


      if(!cached[cKey]){
          const tmp = generateCachedModel(key,h1,s1,b1,h2,s2,b2);

          cached[cKey] = tmp;
      }

      return cached[cKey];
    }





    onMounted(async () => {
        L = useGlobalLeaflet
          ? WINDOW_OR_GLOBAL.L
          : await import("leaflet/dist/leaflet-src.esm");

          L.interpolatePosition = function(p1, p2, duration, t) {
            var k = t/duration;
            k = (k > 0) ? k : 0;
            k = (k > 1) ? 1 : k;
            return L.latLng(p1.lat + k * (p2.lat - p1.lat),
                p1.lng + k * (p2.lng - p1.lng));
          };




          L.Canvas.include({
              _updateImg(layer) {
                const { img } = layer.options;
                const p = layer._point.round();

                const zFactor = (layer._map._zoom-4)*0.05;

                const status = layer.options.status || 'unknown';

                if(img.hide || img.hidden){
                  return false;
                }


                if(layer.options.isCtrlPressed){

                  this._ctx.save();
                  this._ctx.translate(p.x, p.y);
                  if(status==='online'){
                    this._ctx.filter='hue-rotate(130deg) brightness(1.5)';
                  }else if(status==='offline'){
                    this._ctx.filter='hue-rotate(0deg) brightness(1.5)';
                  }else {
                    this._ctx.filter='hue-rotate(62deg) brightness(2) saturate(1.5)';
                  }
                  this._ctx.drawImage(imgCircle, -(40*(img.rSize+zFactor)) / 2, -(40*(img.rSize+zFactor)) / 2, (40*(img.rSize+zFactor)), (40*(img.rSize+zFactor)));
                  this._ctx.rotate(img.rotate * Math.PI / 180);



                  this._ctx.filter='hue-rotate(0deg) brightness(1) saturate(1)';
                  this._ctx.drawImage(img.canva, -(img.size[0]*((img.rSize+zFactor)-0.2)) / 2, -(img.size[0]*((img.rSize+zFactor)-0.2)) / 2, (img.size[0]*((img.rSize+zFactor)-0.2)), (img.size[0]*((img.rSize+zFactor)-0.2)));


                  this._ctx.restore();
                }else if (img.rotate) {
                  this._ctx.save();

                  this._ctx.translate(p.x, p.y);

                  this._ctx.rotate(img.rotate * Math.PI / 180);

                  this._ctx.drawImage(img.canva, -(img.size[0]*(img.rSize+zFactor)) / 2, -(img.size[0]*(img.rSize+zFactor)) / 2, (img.size[0]*(img.rSize+zFactor)), (img.size[0]*(img.rSize+zFactor)));


                  this._ctx.restore();
                } else {

                  this._ctx.save();
                  this._ctx.translate(p.x, p.y);

                  this._ctx.drawImage(img.canva, -(img.size[0]*(img.rSize+zFactor)) / 2, -(img.size[0]*(img.rSize+zFactor)) / 2, (img.size[0]*(img.rSize+zFactor)), (img.size[0]*(img.rSize+zFactor)));
                  this._ctx.restore();
                }
              },
            });


      // eslint-disable-next-line no-unused-vars
      const angleCrds = (map, prevLatlng, latlng) => {
        if (!latlng || !prevLatlng) return 0;
        const pxStart = map.project(prevLatlng);
        const pxEnd = map.project(latlng);
        return Math.atan2(pxStart.y - pxEnd.y, pxStart.x - pxEnd.x) / Math.PI * 180 - 90;
      };

      // eslint-disable-next-line no-unused-vars
      const defaultImgOptions = {
        rotate: 0,
        size: [40, 40],
        offset: { x: 0, y: 0 },
      };

      L.CanvasMarker = L.CircleMarker.extend({
        _updatePath() {
            this._renderer._updateImg(this);
        },
        updateCanva(d){
          let car = d.category || 'car';
          let h1 = 0;
          let s1 = 0;
          let b1 = 1.8;
          let h2 = 0;
          let s2 = 0;
          let b2 = 1.8;


          if(!bases[car]) {
            car = 'car';
          }


            if(d.attributes['tarkan.color']){
              const tmp = d.attributes['tarkan.color'].split("-");
              h1 = tmp[0];
              s1 = tmp[1];
              b1 = tmp[2];
            }
            if(d.attributes['tarkan.color_extra']){
              const tmp = d.attributes['tarkan.color_extra'].split("-");
              h2 = tmp[0];
              s2 = tmp[1];
              b2 = tmp[2];
            }



            const model = getCachedModel(car,h1,s1,b1,h2,s2,b2);

            this.options.img.canva = model;
            this.options.img.size = [sizes[car],sizes[car]]

            this._updatePath();
        },
        statics: {
          notStartedState: 0,
          endedState: 1,
          pausedState: 2,
          runState: 3
        },

        options: {
          autostart: false,
          loop: false,
        },

        initialize: function (latlngs, durations, options) {
          L.CircleMarker.prototype.initialize.call(this, latlngs[0], options);

          // eslint-disable-next-line no-unused-vars
          this._latlngs = latlngs.map(function(e, index) {
            return L.latLng(e);
          });

          if (durations instanceof Array) {
            this._durations = durations;
          } else {
            this._durations = this._createDurations(this._latlngs, durations);
          }

          this._currentDuration = 0;
          this._currentIndex = 0;

          this._state = L.CanvasMarker.notStartedState;
          this._startTime = 0;
          this._startTimeStamp = 0;  // timestamp given by requestAnimFrame
          this._pauseStartTime = 0;
          this._animId = 0;
          this._animRequested = false;
          this._currentLine = [];
          this._stations = {};
        },

        isRunning: function() {
          return this._state === L.CanvasMarker.runState;
        },

        isEnded: function() {
          return this._state === L.CanvasMarker.endedState;
        },

        isStarted: function() {
          return this._state !== L.CanvasMarker.notStartedState;
        },

        isPaused: function() {
          return this._state === L.CanvasMarker.pausedState;
        },

        start: function() {
          if (this.isRunning()) {
            return;
          }

          if (this.isPaused()) {
            this.resume();
          } else {
            this._loadLine(0);
            this._startAnimation();
            this.fire('start');
          }
        },

        resume: function() {
          if (! this.isPaused()) {
            return;
          }
          // update the current line
          this._currentLine[0] = this.getLatLng();
          this._currentDuration -= (this._pauseStartTime - this._startTime);
          this._startAnimation();
        },

        pause: function() {
          if (! this.isRunning()) {
            return;
          }

          this._pauseStartTime = Date.now();
          this._state = L.CanvasMarker.pausedState;
          this._stopAnimation();
          this._updatePosition();
        },

        stop: function(elapsedTime) {
          if (this.isEnded()) {
            return;
          }

          this._stopAnimation();

          if (typeof(elapsedTime) === 'undefined') {
            // user call
            elapsedTime = 0;
            this._updatePosition();
          }

          this._state = L.CanvasMarker.endedState;
          this.fire('end', {elapsedTime: elapsedTime});
        },

        addLatLng: function(latlng, duration) {
          this._latlngs.push(L.latLng(latlng));
          this._durations.push(duration);
        },
        updateStatus: function(s){
          this._stopAnimation();

          this.options.status = s;

          console.log(s);
          if(this.options.isCtrlPressed) {
            L.Util.requestAnimFrame(function () {
              this.redraw();
            }, this, false);
          }
        },
        setPressed: function(s){
          this._stopAnimation();
          if(this.options.isCtrlPressed !== s) {
            this.options.isCtrlPressed = s;

            L.Util.requestAnimFrame(function () {
              this.redraw();
            }, this, false);
          }
        },
        moveTo: function(latlng, duration) {
          this._stopAnimation();
          this._latlngs = [this.getLatLng(), L.latLng(latlng)];
          this._durations = [duration];
          this._state = L.CanvasMarker.notStartedState;
          this.start();
          this.options.loop = false;
        },
        addToMap: function(){
          addLayer({
            ...props,
            leafletObject: this
          });
        },

        addStation: function(pointIndex, duration) {
          if (pointIndex > this._latlngs.length - 2 || pointIndex < 1) {
            return;
          }
          this._stations[pointIndex] = duration;
        },

        onAdd: function (map) {
          L.CircleMarker.prototype.onAdd.call(this, map);

          if (this.options.autostart && (! this.isStarted())) {
            this.start();
            return;
          }

          if (this.isRunning()) {
            this._resumeAnimation();
          }
        },

        onRemove: function(map) {
          L.CircleMarker.prototype.onRemove.call(this, map);
          this._stopAnimation();
        },

        _createDurations: function (latlngs, duration) {
          var lastIndex = latlngs.length - 1;
          var distances = [];
          var totalDistance = 0;
          var distance = 0;

          // compute array of distances between points
          for (var i = 0; i < lastIndex; i++) {
            distance = latlngs[i + 1].distanceTo(latlngs[i]);
            distances.push(distance);
            totalDistance += distance;
          }

          var ratioDuration = duration / totalDistance;

          var durations = [];
          for (i = 0; i < distances.length; i++) {
            durations.push(distances[i] * ratioDuration);
          }

          return durations;
        },

        _startAnimation: function() {
          this._state = L.CanvasMarker.runState;
          this._animId = L.Util.requestAnimFrame(function(timestamp) {
            this._startTime = Date.now();
            this._startTimeStamp = timestamp;
            this._animate(timestamp);
          }, this, true);
          this._animRequested = true;
        },

        _resumeAnimation: function() {
          if (! this._animRequested) {
            this._animRequested = true;
            this._animId = L.Util.requestAnimFrame(function(timestamp) {
              this._animate(timestamp);
            }, this, true);
          }
        },

        _stopAnimation: function() {
          if (this._animRequested) {
            L.Util.cancelAnimFrame(this._animId);
            this._animRequested = false;
          }
        },

        _updatePosition: function() {
          var elapsedTime = Date.now() - this._startTime;
          this._animate(this._startTimeStamp + elapsedTime, true);
        },

        _loadLine: function(index) {
          this._currentIndex = index;
          this._currentDuration = this._durations[index];
          this._currentLine = this._latlngs.slice(index, index + 2);
        },

        /**
         * Load the line where the marker is
         * @param  {Number} timestamp
         * @return {Number} elapsed time on the current line or null if
         * we reached the end or marker is at a station
         */
        _updateLine: function(timestamp) {
          // time elapsed since the last latlng
          var elapsedTime = timestamp - this._startTimeStamp;

          // not enough time to update the line
          if (elapsedTime <= this._currentDuration) {
            return elapsedTime;
          }

          var lineIndex = this._currentIndex;
          var lineDuration = this._currentDuration;
          var stationDuration;

          while (elapsedTime > lineDuration) {
            // substract time of the current line
            elapsedTime -= lineDuration;
            stationDuration = this._stations[lineIndex + 1];

            // test if there is a station at the end of the line
            if (stationDuration !== undefined) {
              if (elapsedTime < stationDuration) {
                this.setLatLng(this._latlngs[lineIndex + 1]);
                return null;
              }
              elapsedTime -= stationDuration;
            }

            lineIndex++;

            // test if we have reached the end of the polyline
            if (lineIndex >= this._latlngs.length - 1) {

              if (this.options.loop) {
                lineIndex = 0;
                this.fire('loop', {elapsedTime: elapsedTime});
              } else {
                // place the marker at the end, else it would be at
                // the last position
                this.setLatLng(this._latlngs[this._latlngs.length - 1]);
                this.stop(elapsedTime);
                return null;
              }
            }
            lineDuration = this._durations[lineIndex];
          }

          this._loadLine(lineIndex);
          this._startTimeStamp = timestamp - elapsedTime;
          this._startTime = Date.now() - elapsedTime;
          return elapsedTime;
        },

        _animate: function(timestamp, noRequestAnim) {
          this._animRequested = false;

          // find the next line and compute the new elapsedTime
          var elapsedTime = this._updateLine(timestamp);

          if (this.isEnded()) {
            // no need to animate
            return;
          }

          if (elapsedTime != null) {
            // compute the position
            var p = L.interpolatePosition(this._currentLine[0],
                this._currentLine[1],
                this._currentDuration,
                elapsedTime);
            this.setLatLng(p);
          }

          if (! noRequestAnim) {
            this._animId = L.Util.requestAnimFrame(this._animate, this, false);
            this._animRequested = true;
          }
        }
      });













    });



    const addDevice = (d)=>{
          let car = d.category || 'default';
          let h1 = 0;
          let s1 = 0;
          let b1 = 1.8;
          let h2 = 0;
          let s2 = 0;
          let b2 = 1.8;

          if(!bases[car]) {
            car = 'default';
          }

          const entity = leafletRef.value.find((e)=>{ e.options.id === d.id });
          if(entity){
            console.log("ENTITY "+entity.id)
          }else {

            if(d.attributes['tarkan.color']){
              const tmp = d.attributes['tarkan.color'].split("-");
              h1 = tmp[0];
              s1 = tmp[1];
              b1 = tmp[2];
            }
            if(d.attributes['tarkan.color_extra']){
              const tmp = d.attributes['tarkan.color_extra'].split("-");
              h2 = tmp[0];
              s2 = tmp[1];
              b2 = tmp[2];
            }



            const model = getCachedModel(car,h1,s1,b1,h2,s2,b2);




            const latlng = (d.positionData)?L.latLng(d.positionData.latitude, d.positionData.longitude):L.latLng(0,0)

            const tmp = new L.CanvasMarker([latlng],[1000], {
              minZoom: 10,
              radius: radius[car],
              id: d.id,
              status: d.status,
              isCtrlPressed: false,
              img: {
                canva: model,
                hide: !(d.positionData),
                hidden: false,
                car: car,
                rSize: 0.5,
                size: [sizes[car],sizes[car]],     //image size ( default [40, 40] )
                rotate: (d.positionData)?d.positionData.course:0,         //image base rotate ( default 0 )
                offset: {x: 0, y: 0}, //image offset ( default { x: 0, y: 0 } )
              },
            }).on("click",(e)=>{
              context.emit('click',e);
            }).on("mouseover",(e)=>{
              context.emit('mouseover',e);
            }).on("mouseout",(e)=>{
              context.emit('mouseout',e);
            }).on("contextmenu",(e)=>{
              context.emit('contextmenu',e);
            });

            const methods = {};

            addLayer({
              ...props,
              ...methods,
              leafletObject: tmp
            });

            markerList.value.push(tmp);


            return tmp;
          }


    }


    window.loadModels = async ()=>{

      await loadModel('default', 'default', true, false, 30,20  );

      await loadModel('arrow', 'arrow', true, false, 50,17);
      await loadModel('person', 'person', true, false, 50,13);
      await loadModel('animal', 'pet', true, false, 50,13);
      await loadModel('bicycle', 'bicycle', true, false, 85,40);

      await loadModel('motorcycle', 'motorcycle', true, true, 75,40);
      await loadModel('scooter', 'scooter', true, true, 70,30);


      await loadModel('car', 'carroPasseio', true, false, 50,25);
      await loadModel('pickup', 'carroUtilitario', true, false, 55,25);

      await loadModel('van', 'vanUtilitario', true, false, 55,25);
      await loadModel('truck', 'caminhaoBau', true, true, 95,40);

      await loadModel('truck2', 'truckBau', true, true, 90,55);
      await loadModel('truck1', 'truckCavalo', true, false, 75,45);


      await loadModel('tractor', 'tractor', true, false, 55,32);

      await loadModel('boat', 'boat', true, false, 70,40);
      await loadModel('ship', 'ship', true, false, 110,55);
      await loadModel('bus', 'bus', true, false, 95,58);
      await loadModel('train', 'bus', true, false, 110,50);
      await loadModel('tram', 'bus', true, false, 110,50);
      await loadModel('trolleybus', 'bus', true, false, 110,50);


      await loadModel('crane', 'crane', true, false, 110,55);


      await loadModel('plane', 'plane', true, false, 100,60);
      await loadModel('helicopter', 'helicopter', true, false, 100,60);
      await loadModel('offroad', 'offroad', true, false, 70,30);
    }

    window.addDevice = addDevice;
    provide('addDevice',addDevice);

  },
  render(){

    //console.log("Kore is rendering...")

    return null;
  }



}

</script>