<template>
  <div class="radial-menu" ref="context" @blur="doHide()" :class="{ctxshow: show,ctxdisplay: display}" :style="{left: pos.x+'px',top: pos.y+'px'}">
    <ul>
      <div class="toggle"></div>
      <li style="--i: 0;" class="active"><a href=""><i class="fas fa-car"></i></a></li>
      <li style="--i: 1;"><a href=""><i class="fas fa-lock"></i></a></li>
      <li style="--i: 2;"><a href=""><i class="fas fa-anchor"></i></a></li>
      <li style="--i: 3;"><a href=""><i class="fas fa-share-square"></i></a></li>
      <li style="--i: 4;"><a href=""><i class="fas fa-align-right"></i></a></li>
      <li style="--i: 5;"><a href=""><i class="fas fa-terminal"></i></a></li>
      <li style="--i: 6;"><a href=""><i class="fas fa-link"></i></a></li>
      <li style="--i: 7;"><a href=""><i class="fas fa-edit"></i></a></li>


      <div class="indicator2"></div>
    </ul>
  </div>
</template>



<script setup>

import {ref,nextTick,onMounted,defineExpose} from 'vue';

const show = ref(false)
const display = ref(false)
const menus = ref([])
const submenu = ref([])
const pos = ref({x: 200,y: 200,subX: 0,subY: 0});



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
  setTimeout(()=> {
    nextTick(() => {
      show.value = true;
      nextTick(() => {
        /*
      //const elSize = document.querySelector("#context").getBoundingClientRect();
      const documentSize = document.body.getBoundingClientRect();

      if (args.evt.clientX > (documentSize.width / 2)) {
        pos.value.x = (args.evt.clientX - 300) - 5;
      } else {
        pos.value.x = args.evt.clientX + 5;
      }


      if (args.evt.clientY > (documentSize.height / 2)) {
        pos.value.y = (args.evt.clientY - 300) - 5;
      } else {
        pos.value.y = args.evt.clientY + 5;
      }*/

        pos.value.x = (args.evt.clientX - 150);
        pos.value.y = (args.evt.clientY - 150);
      });

    });
  },10);
}


/*
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
}*/

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

<style>

.radial-menu{
  position: absolute;
  z-index: 999999999999999;
  width: 300px;
  height: 300px;
  opacity: 0.1;
  transition: 0.1s;
  display: none;
}


.radial-menu.ctxdisplay{
  display: block;
}

.radial-menu.ctxshow{
  opacity: 1;
}

.radial-menu ul{
  position: relative;
  width: 300px;
  height: 300px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.radial-menu ul .toggle{
  position: absolute;
  width: 75px;
  height: 75px;
  background: var(--el-color-primary);
  color: black;
  background: transparent;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: 0.5s;
}

.radial-menu.ctxshow ul .toggle{
  box-shadow: 0 0 0 60px var(--el-color-primary), 0 0 0 68px var(--el-color-primary)
}

.radial-menu ul li{
  position: absolute;
  list-style: none;
  left: 10px;
  transform: rotate(calc(360deg / 8 * var(--i))) translateX(40px);
  transform-origin:  140px;
  z-index: 10;
  opacity: 0;
  transition: 0.5s;
}

.radial-menu.ctxshow ul li{
  opacity: 1;
}

.radial-menu ul li a{
  display: flex;
  justify-content: center;
  align-items: center;
  width: 55px;
  height: 55px;
  transform: rotate(calc(360deg / -8 * var(--i)));
  border-radius: 50%;
  font-size: 1.7em;
  color: var(--el-bg-color);
  text-decoration: none;
}

.radial-menu ul li:hover{

  transform: rotate(calc(360deg / 8 * var(--i))) translateX(12px);
}

.indicator2{
  position: absolute;
  left: calc(50% + 2.5px);
  width: 100px;
  height: 1px;
  pointer-events: none;
  transform-origin: right;
}

.indicator2:before{
  content: ' ';
  position: absolute;
  top: -27.5px;
  left: 72px;
  width: 55px;
  height: 55px;
  background: var(--el-text-color-primary);
  box-shadow: 0 0 0 6px var(--el-color-primary);
  border-radius: 50%;
  transition: all 0.3s;
  opacity: 0;
}



.radial-menu ul li:nth-child(2):hover ~ .indicator2{
  transform: translateX(-103px) rotate(0deg);
  display: block;
}


.radial-menu ul li:nth-child(2):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}

.radial-menu ul li:nth-child(3):hover ~ .indicator2{
  transform: translateX(-103px) rotate(45deg);
  display: block;
}

.radial-menu ul li:nth-child(3):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}


.radial-menu ul li:nth-child(4):hover ~ .indicator2{
  transform: translateX(-103px) rotate(90deg);
  display: block;
}

.radial-menu ul li:nth-child(4):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}

.radial-menu ul li:nth-child(5):hover ~ .indicator2{
  transform: translateX(-103px) rotate(135deg);
  display: block;
}

.radial-menu ul li:nth-child(5):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}


.radial-menu ul li:nth-child(6):hover ~ .indicator2{
  transform: translateX(-103px) rotate(180deg);
  display: block;
}

.radial-menu ul li:nth-child(6):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}


.radial-menu ul li:nth-child(7):hover ~ .indicator2{
  transform: translateX(-103px) rotate(225deg);
  display: block;
}

.radial-menu ul li:nth-child(7):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}


.radial-menu ul li:nth-child(8):hover ~ .indicator2{
  transform: translateX(-103px) rotate(270deg);
  display: block;
}

.radial-menu ul li:nth-child(8):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}



.radial-menu ul li:nth-child(9):hover ~ .indicator2{
  transform: translateX(-103px) rotate(315deg);
  display: block;
}

.radial-menu ul li:nth-child(9):hover ~ .indicator2:before{
  opacity: 1 !important;
  left: -27.5px;
}
</style>