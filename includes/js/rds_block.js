// Business Survey gutenberg block
!function(e){function t(r){if(n[r])return n[r].exports;var i=n[r]={i:r,l:!1,exports:{}};return e[r].call(i.exports,i,i.exports,t),i.l=!0,i.exports}var n={};t.m=e,t.c=n,t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:r})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=0)}([function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n(1);n.n(r)},function(e,t){var n=wp.i18n.__,r=wp.blocks.registerBlockType,i=wp.components.SelectControl,u={backgroundColor:"#2d2d2d",color:"#fff",padding:"12px",textAlign:"center"},l=[],o=!1;jQuery.ajax({type:"POST",data:{action:"rds_get_RDserve_list"},url:ajaxurl,success:function(e){l=e,l=JSON.parse(l);var t={value:0,label:" -- "};l.unshift(t)}}),r("hdq/RDserve-block",{title:n("business survey"),icon:"clipboard",category:"common",keywords:[n("RDserve"),n("hdRDserve"),n("HD")],attributes:{RDserveId:{type:"string",selector:".hdq-RDserve-id"}},multiple:!1,reusable:!1,edit:function(e){function t(t){e.setAttributes({RDserveId:t})}var n=e.attributes.RDserveId;return!o&&e.isSelected&&(o=!0,setTimeout(function(){jQuery(".rds_gutenberg_block").val(n)},200)),[wp.element.createElement("div",{style:u},"Business Survey: This block will be replaced with the selected RDserve",wp.element.createElement(i,{class:"rds_gutenberg_block",onChange:t,value:e.attributes.QuizId,label:"Select a RDserve",options:l}))]},save:function(e){return wp.element.createElement("div",{className:"hdq-RDserve-gutenberg"},"[HDRDserve RDserve = "+e.attributes.RDserveId+"]")}})}]);