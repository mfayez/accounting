import{J as b}from"./ActionMessage-86d2cefe.js";import{J as k}from"./ActionSection-96b34f83.js";import{J}from"./Button-c7c1269f.js";import{J as V}from"./ConfirmationModal-fc660ee4.js";import{J as y}from"./DangerButton-cae1aa00.js";import{J as S}from"./DialogModal-9ccdd4ea.js";import{J as U}from"./FormSection-fba54185.js";import{J as z}from"./Input-fd3ff2e6.js";import{J as j}from"./Checkbox-f9be08cc.js";import{J as w}from"./InputError-147afb17.js";import{J as C}from"./Label-9c529059.js";import{J as D}from"./SecondaryButton-0e4a566a.js";import{J as I}from"./SectionBorder-790e6c81.js";import{J as B}from"./ValidationErrors-62fdbd60.js";import{M as T}from"./vue3-multiselect.umd.min-18b68693.js";import{r as a,o as q,b as A,w as m,g as f,t as p,e as t,d as n,k as E,n as M}from"./app-a7c937a1.js";/* empty css                                                                  */import{_ as N}from"./_plugin-vue_export-helper-c27b6911.js";import"./SectionTitle-57156eee.js";import"./Modal-d0d07394.js";const G={components:{JetActionMessage:b,JetActionSection:k,JetButton:J,JetConfirmationModal:V,JetDangerButton:y,JetDialogModal:S,JetFormSection:U,JetInput:z,JetCheckbox:j,JetInputError:w,JetLabel:C,JetSecondaryButton:D,JetSectionBorder:I,JetValidationErrors:B,Multiselect:T},props:{},data(){return{currencies:["EGP","USD","EUR","GBP"],settings:[],errors:[],form:this.$inertia.form({type:"application settings",custom_desc:!1,automatic:!1,e_invoice:!0,e_receipt:!1,sales_buzz:!1,mobis_integration:!1,accounting:!1,inventory:!1,serial_number:"00000000-00000000-00000000",invoiceTemplate:"",invoiceVersion:"1.0",currencies:["EGP"]}),showDialog:!1}},methods:{ShowDialog(){this.showDialog=!0,axios.get(route("settings.json"),{params:{type:this.form.type}}).then(i=>{this.settings=i.data,this.form.invoiceTemplate=this.settings.invoiceTemplate,this.form.invoiceVersion=this.settings.invoiceVersion,this.form.currencies=JSON.parse(this.settings.currencies),this.form.automatic=this.settings.automatic=="1",this.form.custom_desc=this.settings.custom_desc=="1",this.form.e_invoice=this.settings.e_invoice=="1",this.form.e_receipt=this.settings.e_receipt=="1",this.form.sales_buzz=this.settings.sales_buzz=="1",this.form.mobis_integration=this.settings.mobis_integration=="1",this.form.accounting=this.settings.accounting=="1",this.form.inventory=this.settings.inventory=="1",this.form.serial_number=this.settings.serial_number}).catch(i=>{})},CancelDlg(){this.showDialog=!1},SaveSettings(){axios.post(route("settings.store"),this.form).then(i=>{this.$nextTick(()=>this.$emit("dataUpdated")),this.form.reset(),this.form.processing=!1,this.showDialog=!1}).catch(i=>{this.form.processing=!1,this.$page.props.errors=i.response.data.errors,this.errors=i.response.data.errors})},submit(){this.SaveSettings()}},created:function(){}},P={class:"grid grid-cols-2 gap-4"},R={class:"col-span-2"},F={class:"col-span-1"},L={class:"col-span-1"},O={class:"col-span-1"},x={class:"col-span-1"},H={class:"col-span-1"},K={class:"col-span-1"},Q={class:"col-span-1"},W={class:"col-span-1"},X=n("div",null,null,-1),Y={class:"col-span-2"},Z={class:"col-span-1"},$={class:"col-span-1"},ee={class:"flex items-center justify-end mt-4"};function oe(i,e,se,te,o,c){const d=a("jet-validation-errors"),r=a("jet-label"),u=a("jet-input"),l=a("jet-checkbox"),_=a("multiselect"),v=a("jet-secondary-button"),g=a("jet-button"),h=a("jet-dialog-modal");return q(),A(h,{show:o.showDialog,"max-width":"lg",onClose:e[18]||(e[18]=s=>o.showDialog=!1)},{title:m(()=>[f(p(i.__("Application Settings")),1)]),content:m(()=>[t(d,{class:"mb-4"}),n("form",{onSubmit:e[16]||(e[16]=E((...s)=>c.submit&&c.submit(...s),["prevent"]))},[n("div",P,[n("div",R,[t(r,{for:"serial_number",value:i.__("Serial Number"),class:"ms-2"},null,8,["value"]),t(u,{id:"serial_number",type:"text",class:"mt-1 block w-full",modelValue:o.form.serial_number,"onUpdate:modelValue":e[0]||(e[0]=s=>o.form.serial_number=s),required:"",autofocus:""},null,8,["modelValue"])]),n("div",F,[t(l,{name:"e_invoice",id:"e_invoice",checked:o.form.e_invoice,"onUpdate:checked":e[1]||(e[1]=s=>o.form.e_invoice=s)},null,8,["checked"]),t(r,{for:"e_invoice",value:i.__("E-Invoice"),class:"ms-2"},null,8,["value"])]),n("div",L,[t(l,{name:"e_receipt",id:"e_receipt",checked:o.form.e_receipt,"onUpdate:checked":e[2]||(e[2]=s=>o.form.e_receipt=s)},null,8,["checked"]),t(r,{for:"e_receipt",value:i.__("E-Receipt"),class:"ms-2"},null,8,["value"])]),n("div",O,[t(l,{name:"inventory",id:"inventory",checked:o.form.inventory,"onUpdate:checked":e[3]||(e[3]=s=>o.form.inventory=s)},null,8,["checked"]),t(r,{for:"inventory",value:i.__("Activate Inventories"),class:"ms-2"},null,8,["value"])]),n("div",x,[t(l,{name:"accounting",id:"accounting",checked:o.form.accounting,"onUpdate:checked":e[4]||(e[4]=s=>o.form.accounting=s)},null,8,["checked"]),t(r,{for:"accounting",value:i.__("Activate Accounting"),class:"ms-2"},null,8,["value"])]),n("div",H,[t(l,{name:"sales_buzz",id:"sales_buzz",checked:o.form.sales_buzz,"onUpdate:checked":e[5]||(e[5]=s=>o.form.sales_buzz=s)},null,8,["checked"]),t(r,{for:"sales_buzz",value:i.__("Sales Buz Integration"),class:"ms-2"},null,8,["value"])]),n("div",K,[t(l,{name:"mobis_integration",id:"mobis_integration",checked:o.form.mobis_integration,"onUpdate:checked":e[6]||(e[6]=s=>o.form.mobis_integration=s)},null,8,["checked"]),t(r,{for:"mobis_integration",value:i.__("Mobis Integration"),class:"ms-2"},null,8,["value"])]),n("div",Q,[t(l,{name:"custom_desc",id:"custom_desc",checked:o.form.custom_desc,"onUpdate:checked":e[7]||(e[7]=s=>o.form.custom_desc=s)},null,8,["checked"]),t(r,{for:"custom_desc",value:i.__("Custom Items Description"),class:"ms-2"},null,8,["value"])]),n("div",W,[t(r,{value:i.__("Currencies")},null,8,["value"]),t(_,{modelValue:o.form.currencies,"onUpdate:modelValue":e[8]||(e[8]=s=>o.form.currencies=s),options:o.currencies,placeholder:i.__("Select Company Currencies"),multiple:!0},null,8,["modelValue","options","placeholder"])]),X,n("div",Y,[t(l,{name:"automatic",id:"automatic",checked:o.form.automatic,"onUpdate:checked":e[9]||(e[9]=s=>o.form.automatic=s)},null,8,["checked"]),t(r,{for:"automatic",value:i.__("Automatic Invoice Number"),class:"ms-2"},null,8,["value"])]),n("div",Z,[t(r,{for:"invoiceVersion",value:i.__("Invoice Version")},null,8,["value"]),t(u,{id:"invoiceVersion",type:"text",class:"mt-1 block w-full",modelValue:o.form.invoiceVersion,"onUpdate:modelValue":e[10]||(e[10]=s=>o.form.invoiceVersion=s),active:o.form.automatic,"onUpdate:active":e[11]||(e[11]=s=>o.form.automatic=s),required:o.form.automatic,"onUpdate:required":e[12]||(e[12]=s=>o.form.automatic=s),autofocus:""},null,8,["modelValue","active","required"])]),n("div",$,[t(r,{for:"invoice_template",value:i.__("Invoice Template")},null,8,["value"]),t(u,{id:"invoice_template",type:"text",class:"mt-1 block w-full",modelValue:o.form.invoiceTemplate,"onUpdate:modelValue":e[13]||(e[13]=s=>o.form.invoiceTemplate=s),active:o.form.automatic,"onUpdate:active":e[14]||(e[14]=s=>o.form.automatic=s),required:o.form.automatic,"onUpdate:required":e[15]||(e[15]=s=>o.form.automatic=s),autofocus:""},null,8,["modelValue","active","required"])])])],32)]),footer:m(()=>[n("div",ee,[t(v,{onClick:e[17]||(e[17]=s=>c.CancelDlg())},{default:m(()=>[f(p(i.__("Cancel")),1)]),_:1}),t(g,{class:M(["ms-2",{"opacity-25":o.form.processing}]),onClick:c.submit,disabled:o.form.processing},{default:m(()=>[f(p(i.__("Save")),1)]),_:1},8,["onClick","class","disabled"])])]),_:1},8,["show"])}const Se=N(G,[["render",oe]]);export{Se as default};
