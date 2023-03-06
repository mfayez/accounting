import{D as y,A as J}from"./AppLayout-1a839f95.js";import{F as N}from"./inertiajs-tables-laravel-query-builder.es-5f1eef6b.js";import D from"./AddEdit-b7c02d6a.js";import{C as P}from"./Confirm-41f29a5d.js";import{J as q}from"./Label-9c529059.js";import x from"./Preview-5fd32342.js";import R from"./UpdateReceived-4bfa8de6.js";import j from"./CreditNote-5d50eb01.js";import{J as A}from"./SecondaryButton-0e4a566a.js";import{J as E}from"./Button-c7c1269f.js";import{J as F}from"./DangerButton-cae1aa00.js";import"./sweetalert.min-cf20c789.js";import{r as h,o as e,b as L,w as l,d as a,e as w,f as r,h as u,F as d,j as c,v as _,t as m,k as b,n as T,g as Z,p as Q,l as U}from"./app-a7c937a1.js";import{_ as z}from"./_plugin-vue_export-helper-c27b6911.js";import"./Edit-01bde3d2.js";import"./ActionMessage-86d2cefe.js";import"./ActionSection-96b34f83.js";import"./SectionTitle-57156eee.js";import"./ConfirmationModal-fc660ee4.js";import"./Modal-d0d07394.js";import"./DialogModal-9ccdd4ea.js";import"./FormSection-fba54185.js";import"./Input-fd3ff2e6.js";import"./Checkbox-f9be08cc.js";import"./InputError-147afb17.js";import"./SectionBorder-790e6c81.js";import"./ValidationErrors-62fdbd60.js";import"./Edit-a799b6f6.js";import"./Edit-1ec525ac.js";import"./vue3-multiselect.umd.min-18b68693.js";/* empty css                                                                  */import"./Load-131c55d3.js";import"./Load-1a88fc17.js";import"./Edit-0cd1ee03.js";import"./Upload-c519c160.js";import"./Upload-feb55a03.js";import"./Settings-519853de.js";import"./Settings-4bb87828.js";import"./Upload-aed85c43.js";import"./Request-bff86671.js";import"./TextField-0eb10881.js";import"./Add-1c7e008b.js";import"./Load-051be2ee.js";import"./Upload-9760ab9e.js";import"./Load-47d081ba.js";import"./ItemsMap-75bf1f86.js";import"./Upload-259c1194.js";import"./Upload-84201142.js";import"./ItemsMap-d1e6a413.js";import"./RequestEx-1ce14a12.js";const M={mixins:[InteractsWithQueryBuilder],components:{Dropdown:y,AppLayout:J,Confirm:P,PreviewInvoice:x,UpdateReceived:R,CreditNote:j,JetLabel:q,Table:N,JetButton:E,JetDangerButton:F,AddEditItem:D,SecondaryButton:A},props:{items:Object},data(){return{invItem:{quantity:1009},cancelReason:"",notSortableCols:["statusReason","receiver.name","receiver.receiver_id","issuerName","receiverId","receiverName"]}},methods:{downloadZip(t){this.invItem=t,window.open(route("archive.download",[t.id]))},nestedIndex:function(t,p){try{var o=p.split(".");return o.length==1?t[p].toString():o.length==2?t[o[0]][o[1]].toString():o.length==3?t[o[0]][o[1]][o[2]].toString():"Unsupported Nested Index"}catch{}return this.__(p)}}},O=t=>(Q("data-v-14dbf989"),t=t(),U(),t),V={class:"py-4"},W={class:"mx-auto sm:px-6 lg:px-8"},$={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-4"},G=["onClick"],H={class:"grid grid-cols-3 w-56"},K=O(()=>a("td",null,null,-1));function X(t,p,o,g,B,v){const I=h("jet-button"),C=h("Table"),S=h("app-layout");return e(),L(S,null,{default:l(()=>[a("div",V,[a("div",W,[a("div",$,[w(C,{filters:t.queryBuilderProps.filters,search:t.queryBuilderProps.search,columns:t.queryBuilderProps.columns,"on-update":t.setQueryBuilder,meta:o.items},{head:l(()=>[a("tr",null,[(e(!0),r(d,null,u(t.queryBuilderProps.columns,(i,n)=>(e(),r(d,{key:n},[B.notSortableCols.includes(n)?c((e(),r("th",{key:0},m(i.label),513)),[[_,t.show(n)]]):c((e(),r("th",{key:1,class:"cursor-pointer",onClick:b(s=>t.sortBy(n),["prevent"])},m(i.label),9,G)),[[_,t.show(n)]])],64))),128)),a("th",{onClick:p[0]||(p[0]=b(()=>{},["prevent"]))},m(t.__("Actions")),1)])]),body:l(()=>[(e(!0),r(d,null,u(o.items.data,i=>(e(),r("tr",{key:i.id,class:T({credit:i.typeName=="c"})},[(e(!0),r(d,null,u(t.queryBuilderProps.columns,(n,s)=>c((e(),r("td",{key:s},[(e(!0),r(d,null,u(v.nestedIndex(i,s).split(","),f=>(e(),r("div",null,m(s=="status"||s=="statusReason"?t.__(f):s=="start_date"||s=="end_date"?new Date(f).toLocaleDateString():f),1))),256))])),[[_,t.show(s)]])),128)),a("div",H,[c(w(I,{class:"me-2 mt-2",onClick:n=>v.downloadZip(i)},{default:l(()=>[Z(m(t.__("ZIP")),1)]),_:2},1032,["onClick"]),[[_,i.status=="Ready"]])]),K],2))),128))]),_:1},8,["filters","search","columns","on-update","meta"])])])])]),_:1})}const Gt=z(M,[["render",X],["__scopeId","data-v-14dbf989"]]);export{Gt as default};
