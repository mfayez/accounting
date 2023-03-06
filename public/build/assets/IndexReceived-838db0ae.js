import{D as V,A as E}from"./AppLayout-1a839f95.js";import{F as J}from"./inertiajs-tables-laravel-query-builder.es-5f1eef6b.js";import A from"./AddEdit-b7c02d6a.js";import{C as q}from"./Confirm-41f29a5d.js";import{J as P}from"./Label-9c529059.js";import k from"./Preview-5fd32342.js";import x from"./UpdateReceived-4bfa8de6.js";import F from"./CreditNote-5d50eb01.js";import{J as L}from"./SecondaryButton-0e4a566a.js";import{J as U}from"./Button-c7c1269f.js";import{J as M}from"./DangerButton-cae1aa00.js";import"./sweetalert.min-cf20c789.js";import{r as d,o as n,b as Q,w as p,e as c,j as u,Q as W,d as a,f as s,h as g,F as h,v,t as m,k as C,n as z,g as I,p as O,l as $}from"./app-a7c937a1.js";import{_ as G}from"./_plugin-vue_export-helper-c27b6911.js";import"./Edit-01bde3d2.js";import"./ActionMessage-86d2cefe.js";import"./ActionSection-96b34f83.js";import"./SectionTitle-57156eee.js";import"./ConfirmationModal-fc660ee4.js";import"./Modal-d0d07394.js";import"./DialogModal-9ccdd4ea.js";import"./FormSection-fba54185.js";import"./Input-fd3ff2e6.js";import"./Checkbox-f9be08cc.js";import"./InputError-147afb17.js";import"./SectionBorder-790e6c81.js";import"./ValidationErrors-62fdbd60.js";import"./Edit-a799b6f6.js";import"./Edit-1ec525ac.js";import"./vue3-multiselect.umd.min-18b68693.js";/* empty css                                                                  */import"./Load-131c55d3.js";import"./Load-1a88fc17.js";import"./Edit-0cd1ee03.js";import"./Upload-c519c160.js";import"./Upload-feb55a03.js";import"./Settings-519853de.js";import"./Settings-4bb87828.js";import"./Upload-aed85c43.js";import"./Request-bff86671.js";import"./TextField-0eb10881.js";import"./Add-1c7e008b.js";import"./Load-051be2ee.js";import"./Upload-9760ab9e.js";import"./Load-47d081ba.js";import"./ItemsMap-75bf1f86.js";import"./Upload-259c1194.js";import"./Upload-84201142.js";import"./ItemsMap-d1e6a413.js";import"./RequestEx-1ce14a12.js";const H={components:{Dropdown:V,AppLayout:E,Confirm:q,PreviewInvoice:k,UpdateReceived:x,CreditNote:F,JetLabel:P,Table:J,JetButton:U,JetDangerButton:M,AddEditItem:A,SecondaryButton:L},props:{items:Object},data(){return{invItem:{quantity:1009},cancelReason:"",notSortableCols:["statusReason","receiver.name","receiver.receiver_id","issuerName","receiverId","receiverName"]}},methods:{openExternal2(e){window.open(route("eta.invoice.download",{uuid:e.uuid}),"_blank")},openExternal(e){window.open(this.$page.props.preview_url+e.uuid+"/share/"+e.longId)},updateReceived(e){this.invItem=e,this.$nextTick(()=>{this.$refs.dlg6.ShowDialog()})},rejectInvoice(e){this.invItem=e,this.$refs.dlg1.ShowModal()},rejectInv2(){axios.post(route("eta.invoices.cancel"),{uuid:this.invItem.uuid,status:"rejected",reason:this.cancelReason}).then(e=>{alert(e.data)}).catch(e=>{alert(e.response.data)})},nestedIndex:function(e,o){try{var r=o.split(".");return r.length==1?e[o].toString():r.length==2?e[r[0]][r[1]].toString():r.length==3?e[r[0]][r[1]][r[2]].toString():"Unsupported Nested Index"}catch{}return"N/A"}}},K=e=>(O("data-v-441fc614"),e=e(),$(),e),X=K(()=>a("option",{value:"Wrong invoice details"},"Wrong invoice details",-1)),Y=[X],Z={class:"py-4"},ee={class:"mx-auto sm:px-6 lg:px-8"},te={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-4"},oe=["onClick"],re={class:"grid grid-cols-3 w-56"};function ne(e,o,r,S,f,_){const j=d("update-received"),B=d("jet-label"),R=d("confirm"),y=d("jet-danger-button"),b=d("jet-button"),D=d("secondary-button"),N=d("Table"),T=d("app-layout");return n(),Q(T,null,{default:p(()=>[c(j,{ref:"dlg6",modelValue:f.invItem,"onUpdate:modelValue":o[0]||(o[0]=t=>f.invItem=t)},null,8,["modelValue"]),c(R,{ref:"dlg1",onConfirmed:o[2]||(o[2]=t=>_.rejectInv2())},{default:p(()=>[c(B,{for:"type",value:"Select rejection reason:"}),u(a("select",{id:"type","onUpdate:modelValue":o[1]||(o[1]=t=>f.cancelReason=t),class:"mt-1 block w-full"},Y,512),[[W,f.cancelReason]])]),_:1},512),a("div",Z,[a("div",ee,[a("div",te,[c(N,{filters:e.queryBuilderProps.filters,search:e.queryBuilderProps.search,columns:e.queryBuilderProps.columns,"on-update":e.setQueryBuilder,meta:r.items},{head:p(()=>[a("tr",null,[(n(!0),s(h,null,g(e.queryBuilderProps.columns,(t,i)=>(n(),s(h,{key:i},[f.notSortableCols.includes(i)?u((n(),s("th",{key:0},m(t.label),513)),[[v,e.show(i)]]):u((n(),s("th",{key:1,class:"cursor-pointer",onClick:C(l=>e.sortBy(i),["prevent"])},m(t.label),9,oe)),[[v,e.show(i)]])],64))),128)),a("th",{onClick:o[3]||(o[3]=C(()=>{},["prevent"]))},m(e.__("Actions")),1)])]),body:p(()=>[(n(!0),s(h,null,g(r.items.data,t=>(n(),s("tr",{key:t.id,class:z({credit:t.typeName=="c"})},[(n(!0),s(h,null,g(e.queryBuilderProps.columns,(i,l)=>u((n(),s("td",{key:l},[(n(!0),s(h,null,g(_.nestedIndex(t,l).split(","),w=>(n(),s("div",null,m(l=="status"||l=="statusReason"?e.__(w):l=="dateTimeIssued"||l=="dateTimeReceived"?new Date(w).toLocaleDateString():w),1))),256))])),[[v,e.show(l)]])),128)),a("td",null,[a("div",re,[u(c(y,{class:"me-2 mt-2",onClick:i=>_.rejectInvoice(t)},{default:p(()=>[I(m(e.__("Reject")),1)]),_:2},1032,["onClick"]),[[v,t.status=="Valid"]]),u(c(b,{class:"me-2 mt-2",onClick:i=>_.updateReceived(t)},{default:p(()=>[I(m(e.__("Direction")),1)]),_:2},1032,["onClick"]),[[v,t.status=="Valid"]]),u(c(D,{class:"me-2 mt-2",onClick:i=>_.openExternal(t)},{default:p(()=>[I(m(e.__("ETA1")),1)]),_:2},1032,["onClick"]),[[v,t.status=="Valid"]]),u(c(b,{class:"me-2 mt-2",onClick:i=>_.openExternal2(t)},{default:p(()=>[I(m(e.__("ETA2")),1)]),_:2},1032,["onClick"]),[[v,t.status=="Valid"]])])])],2))),128))]),_:1},8,["filters","search","columns","on-update","meta"])])])])]),_:1})}const tt=G(H,[["render",ne],["__scopeId","data-v-441fc614"]]);export{tt as default};
