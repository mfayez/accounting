import{A as b}from"./AppLayout-1a839f95.js";import{F as I}from"./inertiajs-tables-laravel-query-builder.es-5f1eef6b.js";import{J as x}from"./Button-c7c1269f.js";import F from"./AddEdit-b7c02d6a.js";import{r as m,o as r,b as j,w as n,d as e,e as l,f as i,h as u,j as _,v as f,t as h,F as c,k,p as q,l as L}from"./app-a7c937a1.js";import{_ as P}from"./_plugin-vue_export-helper-c27b6911.js";import"./Edit-01bde3d2.js";import"./ActionMessage-86d2cefe.js";import"./ActionSection-96b34f83.js";import"./SectionTitle-57156eee.js";import"./ConfirmationModal-fc660ee4.js";import"./Modal-d0d07394.js";import"./DangerButton-cae1aa00.js";import"./DialogModal-9ccdd4ea.js";import"./FormSection-fba54185.js";import"./Input-fd3ff2e6.js";import"./Checkbox-f9be08cc.js";import"./InputError-147afb17.js";import"./Label-9c529059.js";import"./SecondaryButton-0e4a566a.js";import"./SectionBorder-790e6c81.js";import"./ValidationErrors-62fdbd60.js";import"./Edit-a799b6f6.js";import"./Edit-1ec525ac.js";import"./vue3-multiselect.umd.min-18b68693.js";/* empty css                                                                  */import"./Load-131c55d3.js";import"./Load-1a88fc17.js";import"./Edit-0cd1ee03.js";import"./Upload-c519c160.js";import"./Upload-feb55a03.js";import"./Settings-519853de.js";import"./Settings-4bb87828.js";import"./Upload-aed85c43.js";import"./Request-bff86671.js";import"./TextField-0eb10881.js";import"./Add-1c7e008b.js";import"./Load-051be2ee.js";import"./Upload-9760ab9e.js";import"./Load-47d081ba.js";import"./sweetalert.min-cf20c789.js";import"./ItemsMap-75bf1f86.js";import"./Upload-259c1194.js";import"./Upload-84201142.js";import"./ItemsMap-d1e6a413.js";import"./RequestEx-1ce14a12.js";const S={components:{AppLayout:b,Table:I,JetButton:x,AddEditItem:F},props:{items:Object},methods:{nestedIndex:function(t,s){var o=s.split(".");return o.length==1?t[s]:o.length==2?t[o[0]][o[1]]:o.length==3?t[o[0]][o[1]][o[2]]:"Unsupported Nested Index"},editItem:function(t){}}},A=t=>(q("data-v-a86086ff"),t=t(),L(),t),M={class:"py-4"},V={class:"mx-auto sm:px-6 lg:px-8"},z={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-4"},C={class:"grid justify-items-center"},N=A(()=>e("svg",{xmlns:"http://www.w3.org/2000/svg",height:"24px",viewBox:"0 0 24 24",width:"24px",fill:"#FFFFFF"},[e("path",{d:"M0 0h24v24H0V0z",fill:"none"}),e("path",{d:"M14.06 9.02l.92.92L5.92 19H5v-.92l9.06-9.06M17.66 3c-.25 0-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29zm-3.6 3.19L3 17.25V21h3.75L17.81 9.94l-3.75-3.75z"})],-1));function T(t,s,o,D,E,v){const w=m("jet-button"),y=m("add-edit-item"),g=m("Table"),B=m("app-layout");return r(),j(B,null,{default:n(()=>[e("div",M,[e("div",V,[e("div",z,[l(g,{filters:t.queryBuilderProps.filters,search:t.queryBuilderProps.search,columns:t.queryBuilderProps.columns,"on-update":t.setQueryBuilder,meta:o.items},{head:n(()=>[e("tr",null,[(r(!0),i(c,null,u(t.queryBuilderProps.columns,(p,d)=>_((r(),i("th",{key:d},h(p.label),1)),[[f,t.show(d)]])),128)),e("th",{onClick:s[0]||(s[0]=k(()=>{},["prevent"]))},"Actions")])]),body:n(()=>[(r(!0),i(c,null,u(o.items.data,p=>(r(),i("tr",{key:p.id},[(r(!0),i(c,null,u(t.queryBuilderProps.columns,(d,a)=>_((r(),i("td",{key:a},h(v.nestedIndex(p,a)),1)),[[f,t.show(a)]])),128)),e("td",null,[e("div",C,[l(y,{item:p},{default:n(()=>[l(w,{type:"button"},{default:n(()=>[N]),_:1})]),_:2},1032,["item"])])])]))),128))]),_:1},8,["filters","search","columns","on-update","meta"])])])])]),_:1})}const Mt=P(S,[["render",T],["__scopeId","data-v-a86086ff"]]);export{Mt as default};