import{D as T,A as J}from"./AppLayout-1a839f95.js";import{F as A}from"./inertiajs-tables-laravel-query-builder.es-5f1eef6b.js";import D from"./AddEdit-b7c02d6a.js";import{C as N}from"./Confirm-41f29a5d.js";import{J as P}from"./Label-9c529059.js";import q from"./Preview-5fd32342.js";import{J as y}from"./SecondaryButton-0e4a566a.js";import{J as j}from"./Button-c7c1269f.js";import{J as E}from"./DangerButton-cae1aa00.js";import{r as h,o as r,b as F,w as l,d as m,e as g,f as o,h as u,F as p,j as c,v as _,t as d,k as b,g as L}from"./app-a7c937a1.js";import{_ as R}from"./_plugin-vue_export-helper-c27b6911.js";import"./Edit-01bde3d2.js";import"./ActionMessage-86d2cefe.js";import"./ActionSection-96b34f83.js";import"./SectionTitle-57156eee.js";import"./ConfirmationModal-fc660ee4.js";import"./Modal-d0d07394.js";import"./DialogModal-9ccdd4ea.js";import"./FormSection-fba54185.js";import"./Input-fd3ff2e6.js";import"./Checkbox-f9be08cc.js";import"./InputError-147afb17.js";import"./SectionBorder-790e6c81.js";import"./ValidationErrors-62fdbd60.js";import"./Edit-a799b6f6.js";import"./Edit-1ec525ac.js";import"./vue3-multiselect.umd.min-18b68693.js";/* empty css                                                                  */import"./Load-131c55d3.js";import"./Load-1a88fc17.js";import"./Edit-0cd1ee03.js";import"./Upload-c519c160.js";import"./Upload-feb55a03.js";import"./Settings-519853de.js";import"./Settings-4bb87828.js";import"./Upload-aed85c43.js";import"./Request-bff86671.js";import"./TextField-0eb10881.js";import"./Add-1c7e008b.js";import"./Load-051be2ee.js";import"./Upload-9760ab9e.js";import"./Load-47d081ba.js";import"./sweetalert.min-cf20c789.js";import"./ItemsMap-75bf1f86.js";import"./Upload-259c1194.js";import"./Upload-84201142.js";import"./ItemsMap-d1e6a413.js";import"./RequestEx-1ce14a12.js";const x={components:{Dropdown:T,AppLayout:J,Confirm:N,PreviewInvoice:q,JetLabel:P,Table:A,JetButton:j,JetDangerButton:E,AddEditItem:D,SecondaryButton:y},props:{items:Object},data(){return{invItem:{},notSortableCols:["statusReason","receiver.name","receiver.receiver_id","issuerName","receiverId","receiverName"]}},methods:{sendToETA(t){this.invItem=t,axios.post(route("eta.receipts.send"),{id:this.invItem.id}).then(s=>{}).catch(s=>{})},nestedIndex:function(t,s){try{var e=s.split(".");return e.length==1?t[s].toString():e.length==2?t[e[0]][e[1]].toString():e.length==3?t[e[0]][e[1]][e[2]].toString():"Unsupported Nested Index"}catch{}return"N/A"}}},M={class:"py-4"},O={class:"mx-auto sm:px-6 lg:px-8"},Q={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-4"},U=["onClick"],V={class:"grid grid-cols-3 w-56"};function $(t,s,e,B,w,v){const I=h("jet-button"),C=h("Table"),S=h("app-layout");return r(),F(S,null,{default:l(()=>[m("div",M,[m("div",O,[m("div",Q,[g(C,{filters:t.queryBuilderProps.filters,search:t.queryBuilderProps.search,columns:t.queryBuilderProps.columns,"on-update":t.setQueryBuilder,meta:e.items},{head:l(()=>[m("tr",null,[(r(!0),o(p,null,u(t.queryBuilderProps.columns,(n,a)=>(r(),o(p,{key:a},[w.notSortableCols.includes(a)?c((r(),o("th",{key:0},d(n.label),513)),[[_,t.show(a)]]):c((r(),o("th",{key:1,class:"cursor-pointer",onClick:b(i=>t.sortBy(a),["prevent"])},d(n.label),9,U)),[[_,t.show(a)]])],64))),128)),m("th",{onClick:s[0]||(s[0]=b(()=>{},["prevent"]))},d(t.__("Actions")),1)])]),body:l(()=>[(r(!0),o(p,null,u(e.items.data,n=>(r(),o("tr",{key:n.id},[(r(!0),o(p,null,u(t.queryBuilderProps.columns,(a,i)=>c((r(),o("td",{key:i},[(r(!0),o(p,null,u(v.nestedIndex(n,i).split(","),f=>(r(),o("div",null,d(i=="status"||i=="statusReason"?t.__(f):i=="dateTimeIssued"||i=="dateTimeReceived"?new Date(f).toLocaleDateString():f),1))),256))])),[[_,t.show(i)]])),128)),m("td",null,[m("div",V,[c(g(I,{class:"me-2 mt-2",onClick:a=>v.sendToETA(n)},{default:l(()=>[L(d(t.__("Send")),1)]),_:2},1032,["onClick"]),[[_,n.status=="In Review"]])])])]))),128))]),_:1},8,["filters","search","columns","on-update","meta"])])])])]),_:1})}const Mt=R(x,[["render",$],["__scopeId","data-v-106eb1fd"]]);export{Mt as default};
