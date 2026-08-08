<template>
  <div>
  <div id="context" ref="context" @blur="doHide()" :class="{ctxshow: show,ctxdisplay: display}" :style="{left: pos.x+'px',top: pos.y+'px'}">
      <ul>
        <li v-for="(o,ok) in menus" :key="ok" @click.self="(o.cb && !o.disabled)?o.cb():emptyHandle()" @mouseover.self="submenuHandle($event,o.submenu)"  :class="{'separator': o.text==='separator','disabled': o.disabled}">
          {{o.text}}
          <template v-if="o.submenu">
            <i style="color:#595959;float: right;margin-left: 20px;margin-right: -7px;font-size: 16px;line-height: 18px;" class="fas fa-caret-right"></i>
            <div v-if="submenu" ref="sub" id="submenu" :style="{left: pos.subX+'px',top: pos.subY+'px'}">
              <ul>
                <li v-for="(s,sk) in submenu" :key="'smenu'+sk" :class="{'separator': s.text==='separator','disabled': s.disabled}" @click="(s.cb && !s.disabled)?s.cb():emptyHandle()">{{s.text}}</li>
              </ul>
            </div>
          </template>
        </li>
      </ul>
  </div>
  </div>
</template>

<script setup>

import {ref,nextTick,onMounted,defineExpose} from 'vue';

const show = ref(false)
const display = ref(false)
const menus = ref([])
const submenu = ref([])
const pos = ref({x: 0,y: 0,subX: 0,subY: 0});



onMounted(()=>{

  window.addEventListener("mousedown",(event)=>{

    if(event.button===2){ return false }

    if(show.value || display.value){
      doHide();
    }
  })

})


const openMenu = (args)=>{

    menus.value = args.menus;
    submenu.value = [];

    display.value = true;
    nextTick(()=> {
      show.value = true;
      nextTick(() => {
        const elSize = document.querySelector("#context").getBoundingClientRect();
        const documentSize = document.body.getBoundingClientRect();

        if (args.evt.clientX > (documentSize.width / 2)) {
          pos.value.x = (args.evt.clientX - elSize.width) - 5;
        } else {
          pos.value.x = args.evt.clientX + 5;
        }


        if (args.evt.clientY > (documentSize.height / 2)) {
          pos.value.y = (args.evt.clientY - elSize.height) - 5;
        } else {
          pos.value.y = args.evt.clientY + 5;
        }
      });

    });
}



const submenuHandle = (evt,s)=>{
  if(!s || s.length===0){
    submenu.value = [];
    return false;
  }
  submenu.value = s;

  nextTick(() => {

    const elSize = document.querySelector("li #submenu").getBoundingClientRect();
    const tgSize = evt.target.getBoundingClientRect();
    const documentSize = document.body.getBoundingClientRect();

    if (((tgSize.x+tgSize.width) + elSize.width) > (documentSize.width * 0.8)) {
      pos.value.subX = (tgSize.x) - (elSize.width - 10);
    } else {
      pos.value.subX = (tgSize.x+tgSize.width) - 10;
    }

    if ((evt.clientY + elSize.height) > (documentSize.height * 0.8)) {
      pos.value.subY = (tgSize.y+(tgSize.height/2)) - (elSize.height/2);
    } else {
      pos.value.subY = tgSize.y ;
    }
  });
}

const emptyHandle = ()=>{
  console.log("empty handle");
}

const doHide = ()=>{
  setTimeout(()=> {
    show.value = false;
    nextTick(() => {
      display.value = false;
    });
  },300);
}

defineExpose({openMenu});
</script>

<style scoped>
#context,#submenu{
  display: none;
  opacity: 0.01;
  background: white;
  border-radius: 5px;
  border: #eeeeee 1px solid;
  box-shadow: rgba(0,0,0,0.1) 1px 1px 5px;
  position: absolute;
  z-index: 999999999999999;
  left: 0px;
  top: 0px;
  transition: opacity 150ms ease-in-out;
}

#context.ctxdisplay{
  display: block;
}

#context.ctxshow{
  opacity: 1;
}

#context ul,#submenu ul{
  list-style: none;
  margin: 0px;
  padding: 0px;
}

#context li,#submenu ul li{
  padding: 6px;
  font-size: 12px;
  padding-left: 20px;
  padding-right: 20px;
  cursor: pointer;
  user-select: none;
  border-bottom: rgba(192, 192, 192, 0.20) 1px solid;
}

#context ul li:not(.separator):hover{
  background: #f5f5f5;
}


#submenu{
  display: block;
  opacity: 1;
  color: black;
  background: white;
  position: fixed;
  left: 0px;
  top: 0px;
  z-index: 999999999999999999 !important;
}

#submenu ul{

}


#context ul li:hover #submenu{
  display: block;
}


#context ul li.separator,#submenu ul li.separator{
  color: transparent;
  border-bottom: silver 1px dotted;
  overflow: hidden !important;
  height: 0px !important;
  margin-top: 0px !important;
  padding: 0px !important;
}

.disabled{
  color: silver !important;
  cursor: not-allowed !important;
}
</style>