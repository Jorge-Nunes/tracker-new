<script>

import {inject, onMounted, ref, provide,nextTick} from "vue";
import {GLOBAL_LEAFLET_OPT, WINDOW_OR_GLOBAL} from "@vue-leaflet/vue-leaflet/src/utils";


export default{
  name: 'Kore-CanvaMarker',
  props: ['devices'],
  // eslint-disable-next-line no-unused-vars
  setup(props,context){

    let globalL = WINDOW_OR_GLOBAL.L;

    const leafletRef = ref([]);
    const useGlobalLeaflet = inject(GLOBAL_LEAFLET_OPT);

    const addLayer = inject("addLayer");
    //const options = {};

    const markerList = ref([]);

    const bases = {}
    const color1 = {}
    const color2 = {}
    const sizes = {};

    function loadModel(key,model,c1,c2,w) {

      bases[key] = document.createElement('img');
      bases[key].src = '/img/cars/'+model+'_base.png';

      sizes[key] = w;

      if(c1) {
        color1[key] = document.createElement('img');
        color1[key].src = '/img/cars/'+model+'_color1.png';
      }

      if(c2) {
        color2[key] = document.createElement('img');
        color2[key].src = '/img/cars/'+model+'_color2.png';
      }
    }

    loadModel('passeio','carroPasseio',true,false,60);
    loadModel('utilitario','carroUtilitario',true,false,55);

    loadModel('van','vanUtilitario',true,false,65);
    loadModel('truck','truckCavalo',true,false,75);
    loadModel('truckBau','truckBau',true,true,90);
    loadModel('truckCargo','caminhaoBau',true,true,80);


    const animateMarkers = ()=>{
      console.log("i was "+markerList.value.length+" to check");
      markerList.value.forEach((m)=>{
        if(!m.options.moveCompleted){
            console.log("device "+m.options.id+" need to move");
        }


      })

     // setTimeout(animateMarkers,1000);
    }



    onMounted(async () => {
        globalL = useGlobalLeaflet
          ? WINDOW_OR_GLOBAL.L
          : await import("leaflet/dist/leaflet-src.esm");

          globalL.interpolatePosition = function(p1, p2, duration, t) {
            var k = t/duration;
            k = (k > 0) ? k : 0;
            k = (k > 1) ? 1 : k;
            return globalL.latLng(p1.lat + k * (p2.lat - p1.lat),
                p1.lng + k * (p2.lng - p1.lng));
          };




          globalL.Canvas.include({
              _updateImg(layer) {
                const { img } = layer.options;
                const p = layer._point.round();

                const t = img.car || 'passeio';

                if(img.hide){
                  return false;
                }

                if (img.rotate) {
                  this._ctx.save();

                  this._ctx.translate(p.x, p.y);

                  this._ctx.rotate(img.rotate * Math.PI / 180);

                  this._ctx.drawImage(bases[t], -sizes[t] / 2, -sizes[t] / 2, sizes[t], sizes[t]);
                  if(color1[t]) {
                    this._ctx.filter = img.color1;
                    this._ctx.drawImage(color1[t], -sizes[t] / 2, -sizes[t] / 2, sizes[t], sizes[t]);
                  }

                  if(color2[t]) {
                    this._ctx.filter = img.color2;
                    this._ctx.drawImage(color2[t], -sizes[t] / 2, -sizes[t] / 2, sizes[t],sizes[t]);
                  }

                  this._ctx.restore();
                } else {

                  this._ctx.save();
                  this._ctx.translate(p.x, p.y);

                  this._ctx.drawImage(bases[t], -sizes[t] / 2, -sizes[t] / 2, sizes[t], sizes[t]);
                  if(color1[t]) {
                    this._ctx.filter = img.color1;
                    this._ctx.drawImage(color1[t], -sizes[t] / 2, -sizes[t] / 2, sizes[t], sizes[t]);
                  }

                  if(color2[t]) {
                    this._ctx.filter = img.color2;
                    this._ctx.drawImage(color2[t], -sizes[t] / 2, -sizes[t] / 2, sizes[t],sizes[t]);
                  }

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

      globalL.CanvasMarker = globalL.CircleMarker.extend({
        _updatePath() {
            this._renderer._updateImg(this);
        },
        // eslint-disable-next-line no-unused-vars
        setPosition(lat,lng,course,follow){

          this.options.moveCompleted = false;
          this.options.moveTo = globalL.latLng(lat,lng);
          this.options.moveCourse = course;

          const frames = 150;

          if(this.interval){
            clearInterval(this.interval);
          }

          const oldLatLng = this.getLatLng();
          const oldCourse = this.options.img.rotate;

          const courseDiff = course - oldCourse;
          const latDiff = lat -oldLatLng.lat;
          const lonDiff = lng - oldLatLng.lng;

          const latAdd = latDiff / frames;
          const lonAdd = lonDiff / frames;
          const courseAdd = courseDiff / 50;


          let lp = 0;
          let lastTimestamp = new Date().getTime();

          const animar = ()=>{


            const elapsedTime = new Date().getTime() - lastTimestamp;



            var p = globalL.interpolatePosition(oldLatLng,
                globalL.latLng(lat,lng),
                100,
                elapsedTime);
            this.setLatLng(p);

            console.log(p);


            this._animId = globalL.Util.requestAnimFrame(animar, this, false);

            /*
            const LatLng = this.getLatLng();

            const lat = LatLng.lat + latAdd;
            const lon = LatLng.lng + lonAdd;

            this.setLatLng(globalL.latLng(lat,lon));

            if(follow){
              this._map.setView(globalL.latLng(lat,lon),15)
            }

            if(courseAdd>0 && this.options.img.rotate<course){
              this.options.img.rotate += courseAdd;
            }else if(courseAdd<0 && this.options.img.rotate>course){
              this.options.img.rotate += courseAdd;
            }

            lp++;

            if(lp<150) {
              this._animId = globalL.Util.requestAnimFrame(animar, this, false);
            }*/
          }

          this._animId = globalL.Util.requestAnimFrame(animar, this, true);


          //this.setLatLng(globalL.latLng(lat,lng));
          //this.options.img.rotate = course;
        }
      });













    });



    const addDevice = (d)=>{
          let car = 'passeio';
          let f1 = '';
          let f2 = '';

          const entity = leafletRef.value.find((e)=>{ e.options.id === d.id });
          if(entity){
            console.log("ENTITY "+entity.id)
          }else {

            if(d.attributes['tarkan.car']){
              car = d.attributes['tarkan.car'];
              f1 = d.attributes['tarkan.color'];
              f2 = d.attributes['tarkan.color_extra'];

              if(!bases[car]){
                car = 'passeio';
              }

            }else {


              const R = Math.floor((Math.random() * 6) + 1);
              const K = Object.keys(bases);

              car = K[R] || 'passeio';

              const H1 = Math.floor((Math.random() * 360) + 1);
              const S1 = Math.random();
              const B1 = Math.random() + 0.5;

              const H2 = Math.floor((Math.random() * 360) + 1);
              const S2 = Math.random();
              const B2 = Math.random() + 0.5;

              f1 = 'hue-rotate('+H1+'deg) saturate('+S1+') brightness('+B1+')';
              f2 = 'hue-rotate('+H2+'deg) saturate('+S2+') brightness('+B2+')';
            }


            const latlng = (d.positionData)?globalL.latLng(d.positionData.latitude, d.positionData.longitude):globalL.latLng(0,0)

            const tmp = new globalL.CanvasMarker(latlng, {
              minZoom: 10,
              radius: 30,
              id: d.id,
              moveTo: latlng,
              moveCourse: (d.positionData)?d.positionData.course:0,
              moveCompleted: true,
              img: {
                hide: !(d.positionData),
                car: car,
                color1: f1,
                color2: f2,
                size: [70, 70],     //image size ( default [40, 40] )
                rotate: (d.positionData)?d.positionData.course:0,         //image base rotate ( default 0 )
                offset: {x: 0, y: 0}, //image offset ( default { x: 0, y: 0 } )
              },
            }).on("click",(e)=>{
              context.emit('click',e);

            }).on("mouseover",(e)=>{
              context.emit('mouseover',e);
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

    window.addDevice = addDevice;
    provide('addDevice',addDevice);

  },
  render(){

    //console.log("Kore is rendering...")

    return null;
  }



}

</script>