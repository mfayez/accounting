import{J}from"./ActionMessage-86d2cefe.js";import{J as y}from"./ActionSection-96b34f83.js";import{J as x}from"./Button-c7c1269f.js";import{J as w}from"./ConfirmationModal-fc660ee4.js";import{J as V}from"./DangerButton-cae1aa00.js";import{J as j}from"./DialogModal-9ccdd4ea.js";import{J as C}from"./FormSection-fba54185.js";import{J as k}from"./Input-fd3ff2e6.js";import{J as D}from"./Checkbox-f9be08cc.js";import{J as S}from"./InputError-147afb17.js";import{J as B}from"./Label-9c529059.js";import{J as A}from"./SecondaryButton-0e4a566a.js";import{J as I}from"./SectionBorder-790e6c81.js";import{J as U}from"./ValidationErrors-62fdbd60.js";import{s as v}from"./sweetalert.min-cf20c789.js";import{M as L}from"./vue3-multiselect.umd.min-18b68693.js";/* empty css                                                                  */import{_ as M}from"./_plugin-vue_export-helper-c27b6911.js";import{r as i,o as N,b as z,w as n,g as c,t as m,e as o,d as r,n as E}from"./app-a7c937a1.js";import"./SectionTitle-57156eee.js";import"./Modal-d0d07394.js";const T={components:{JetActionMessage:J,JetActionSection:y,JetButton:x,JetConfirmationModal:w,JetDangerButton:V,JetDialogModal:j,JetFormSection:C,JetInput:k,JetCheckbox:D,JetInputError:S,JetLabel:B,JetSecondaryButton:A,JetSectionBorder:I,JetValidationErrors:U,Multiselect:L},data(){return{errors:[],activities:[],branches:[],progress1:{value:0,maxValue:100},lastDate:"N/A",lastInv:"N/A",processing:!1,form:{value:0,username:"",password:"",buid:"",taxpayerActivityCode:"",period:7,issuer:"",settled_transactions:!1,tax_inverse:!1},showDlg:!1}},methods:{ShowDialog(){this.showDlg=!0,axios.get(route("json.branches")).then(e=>{this.branches=e.data,this.form.issuer=this.branches[0]}).catch(e=>{}),axios.get("/json/ActivityCodes.json").then(e=>{this.activities=e.data,this.form.taxpayerActivityCode=this.activities[0]}).catch(e=>{})},CancelImport(){this.showDlg=!1},LoadSB(){if(this.showDlg==!1){this.form.value=0,this.progress1.value=0,this.progress1.maxValue=100,this.form.username="",this.form.password="",this.form.buid="",this.form.issuer="";return}this.form.value=this.progress1.value+1,axios.post(route("sb.sync_orders"),this.form).then(e=>{if(e.data.code==404){v("Error",e.data.message,"error");return}this.progress1.maxValue=e.data.totalPages,this.lastDate=e.data.lastDate,this.lastInv=e.data.lastInvoice,this.progress1.value=this.progress1.value+1,this.progress1.value<this.progress1.maxValue&&this.$nextTick(()=>this.LoadSB())}).catch(e=>{this.$page.props.errors=e.response.data.errors,this.errors=e.response.data.errors,v({title:__("Error in loading invoices"),icon:"success"})})}}},q={class:"grid grid-cols-2 gap-4"},P={class:"lg:col-span-2"},F={class:"lg:col-span-2"},G={class:"lg:col-span-2 flex justify-center"},H={class:"lg:col-span-2 flex justify-center"},K={class:"mt-8"},O={for:"sync1"},Q=r("br",null,null,-1),R=["value","max"],W={class:"flex items-center justify-end mt-4"};function X(e,t,Y,Z,s,u){const _=i("jet-validation-errors"),a=i("jet-label"),d=i("jet-input"),p=i("jet-checkbox"),f=i("multiselect"),h=i("jet-secondary-button"),g=i("jet-button"),b=i("jet-dialog-modal");return N(),z(b,{show:s.showDlg,onClose:t[7]||(t[7]=l=>s.showDlg=!1)},{title:n(()=>[c(m(e.__("Loading invoices from Sales Buzz")),1)]),content:n(()=>[o(_,{class:"mb-4"}),r("div",q,[r("div",null,[o(a,{for:"username",value:e.__("Username")},null,8,["value"]),o(d,{id:"username",type:"text",class:"mt-1 block w-full",modelValue:s.form.username,"onUpdate:modelValue":t[0]||(t[0]=l=>s.form.username=l),required:"",autofocus:""},null,8,["modelValue"])]),r("div",null,[o(a,{for:"password",value:e.__("Password")},null,8,["value"]),o(d,{id:"password",type:"password",class:"mt-1 block w-full",modelValue:s.form.password,"onUpdate:modelValue":t[1]||(t[1]=l=>s.form.password=l),required:""},null,8,["modelValue"])]),r("div",null,[o(p,{name:"settled_transactions",id:"settled_transactions",checked:s.form.settled_transactions,"onUpdate:checked":t[2]||(t[2]=l=>s.form.settled_transactions=l)},null,8,["checked"]),o(a,{for:"settled_transactions",value:e.__("Setteled Transactions"),class:"ms-2"},null,8,["value"])]),r("div",null,[o(p,{name:"tax_inverse",id:"tax_inverse",checked:s.form.tax_inverse,"onUpdate:checked":t[3]||(t[3]=l=>s.form.tax_inverse=l)},null,8,["checked"]),o(a,{for:"tax_inverse",value:e.__("Calcualte price based on tax"),class:"ms-2"},null,8,["value"])]),r("div",P,[o(a,{value:e.__("Branch")},null,8,["value"]),o(f,{modelValue:s.form.issuer,"onUpdate:modelValue":t[4]||(t[4]=l=>s.form.issuer=l),label:"name",options:s.branches,placeholder:"Select branch"},null,8,["modelValue","options"])]),r("div",F,[o(a,{value:e.__("Branch Activity")},null,8,["value"]),o(f,{modelValue:s.form.taxpayerActivityCode,"onUpdate:modelValue":t[5]||(t[5]=l=>s.form.taxpayerActivityCode=l),label:"Desc_ar",options:s.activities,placeholder:"Select activity"},null,8,["modelValue","options"])]),r("div",G,[o(a,{value:s.lastDate},null,8,["value"])]),r("div",H,[o(a,{value:s.lastInv},null,8,["value"])])]),r("div",K,[r("label",O,m(e.__("Synchronizing Invoices...")),1),Q,r("progress",{class:"w-full",id:"sync1",value:s.progress1.value,max:s.progress1.maxValue},m(s.progress1.value)+"% ",9,R)])]),footer:n(()=>[r("div",W,[o(h,{onClick:t[6]||(t[6]=l=>u.CancelImport())},{default:n(()=>[c(m(e.__("Cancel")),1)]),_:1}),o(g,{class:E(["ms-2",{"opacity-25":s.processing}]),onClick:u.LoadSB,disabled:s.processing},{default:n(()=>[c(m(e.__("Start")),1)]),_:1},8,["onClick","class","disabled"])])]),_:1},8,["show"])}const Je=M(T,[["render",X]]);export{Je as default};
