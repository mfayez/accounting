import{j as s,K as d,o as a,f as n}from"./app-a7c937a1.js";import{_ as i}from"./_plugin-vue_export-helper-c27b6911.js";const u={emits:["update:checked"],props:{checked:{type:[Array,Boolean],default:!1},value:{default:null}},computed:{proxyChecked:{get(){return this.checked},set(e){this.$emit("update:checked",e)}}}},l=["value"];function p(e,o,c,f,h,t){return s((a(),n("input",{type:"checkbox",value:c.value,"onUpdate:modelValue":o[0]||(o[0]=r=>t.proxyChecked=r),class:"rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"},null,8,l)),[[d,t.proxyChecked]])}const x=i(u,[["render",p]]);export{x as J};
