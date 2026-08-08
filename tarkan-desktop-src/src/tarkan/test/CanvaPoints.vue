<script>

import {inject, onMounted,onBeforeUnmount, ref} from "vue";
import {GLOBAL_LEAFLET_OPT, WINDOW_OR_GLOBAL} from "@vue-leaflet/vue-leaflet/src/utils";


export default{
  name: 'Kore-CanvaMarker',
  props: ['points','zoom','map'],
  // eslint-disable-next-line no-unused-vars
  setup(props,context){




    let L = WINDOW_OR_GLOBAL.L;

    const useGlobalLeaflet = inject(GLOBAL_LEAFLET_OPT);

    const addLayer = inject("addLayer");
    //const options = {};

    const markerList = ref([]);




    onMounted(async () => {
      L = useGlobalLeaflet
          ? WINDOW_OR_GLOBAL.L
          : await import("leaflet/dist/leaflet-src.esm");

      if(props.points.length){
        props.points.forEach((p)=>{
          addPoint(p);
        })
      }

    });

    onBeforeUnmount(()=>{
      if(markerList.value.length>0){
        markerList.value.forEach((p)=>{
            p.remove();
        })
      }
    });




    // eslint-disable-next-line no-unused-vars
    const addPoint = (d)=>{


        const latlng = (d)?L.latLng(d[0], d[1]):L.latLng(0,0)

        const tmp = new L.CanvasMarker([latlng],[1000], {
          type: 'pointer',
          minZoom: 10,
          radius: 20,
          id: d.id,
          img: {
            rotate: (d)?d[3]:0,         //image base rotate ( default 0 )
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



  },
  render(){

    //console.log("Kore is rendering...")

    return null;
  }



}

</script>