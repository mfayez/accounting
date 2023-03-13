import{J}from"./ActionMessage-86d2cefe.js";import{J as w}from"./ActionSection-96b34f83.js";import{J as b}from"./Button-c7c1269f.js";import{J as v}from"./ConfirmationModal-fc660ee4.js";import{J as j}from"./DangerButton-cae1aa00.js";import{J as S}from"./DialogModal-9ccdd4ea.js";import{J as k}from"./FormSection-fba54185.js";import{J as D}from"./Input-fd3ff2e6.js";import{J as C}from"./Checkbox-f9be08cc.js";import{J as y}from"./InputError-147afb17.js";import{J as x}from"./Label-9c529059.js";import{J as Q}from"./SecondaryButton-0e4a566a.js";import{J as R}from"./SectionBorder-790e6c81.js";import{J as B}from"./ValidationErrors-62fdbd60.js";import{M as V}from"./vue3-multiselect.umd.min-18b68693.js";import{r,o as M,b as F,w as n,g as m,t as f,e as i,d as a,k as I,n as N}from"./app-a7c937a1.js";/* empty css                                                                  */import{_ as U}from"./_plugin-vue_export-helper-c27b6911.js";import"./SectionTitle-57156eee.js";import"./Modal-d0d07394.js";const A={components:{JetActionMessage:J,JetActionSection:w,JetButton:b,JetConfirmationModal:v,JetDangerButton:j,JetDialogModal:S,JetFormSection:k,JetInput:D,JetCheckbox:C,JetInputError:y,JetLabel:x,JetSecondaryButton:Q,JetSectionBorder:R,JetValidationErrors:B,Multiselect:V},props:{},data(){return{settings:[],errors:[],form:this.$inertia.form({type:"invoice settings",footer:"",showQR:!0}),showDialog:!1}},methods:{ShowDialog(){this.showDialog=!0,axios.get(route("settings.json"),{params:{type:this.form.type}}).then(o=>{this.settings=o.data,this.form.footer=this.settings.footer,this.form.showQR=this.settings.showQR=="1"}).catch(o=>{})},CancelDlg(){this.showDialog=!1},SaveSettings(){axios.post(route("settings.store"),this.form).then(o=>{this.$nextTick(()=>this.$emit("dataUpdated")),this.form.reset(),this.form.processing=!1,this.showDialog=!1}).catch(o=>{this.form.processing=!1,this.$page.props.errors=o.response.data.errors,this.errors=o.response.data.errors})},submit(){this.SaveSettings()}},created:function(){}},E={class:"grid grid-cols-2 gap-4"},T={class:"col-span-2"},q={class:"flex items-center justify-end mt-4"};function z(o,t,L,P,e,l){const p=r("jet-validation-errors"),c=r("jet-label"),d=r("jet-input"),u=r("jet-checkbox"),h=r("jet-secondary-button"),g=r("jet-button"),_=r("jet-dialog-modal");return M(),F(_,{show:e.showDialog,"max-width":"lg",onClose:t[4]||(t[4]=s=>e.showDialog=!1)},{title:n(()=>[m(f(o.__("Invoice PDF Settings")),1)]),content:n(()=>[i(p,{class:"mb-4"}),a("form",{onSubmit:t[2]||(t[2]=I((...s)=>l.submit&&l.submit(...s),["prevent"]))},[a("div",E,[a("div",T,[i(c,{for:"footer",value:o.__("Footer")},null,8,["value"]),i(d,{id:"footer",type:"text",class:"mt-1 block w-full",modelValue:e.form.footer,"onUpdate:modelValue":t[0]||(t[0]=s=>e.form.footer=s),required:"",autofocus:""},null,8,["modelValue"])]),a("div",null,[i(u,{name:"showQR",id:"terms",checked:e.form.showQR,"onUpdate:checked":t[1]||(t[1]=s=>e.form.showQR=s)},null,8,["checked"]),i(c,{for:"QR",value:o.__("Show QR Code"),class:"ms-2"},null,8,["value"])])])],32)]),footer:n(()=>[a("div",q,[i(h,{onClick:t[3]||(t[3]=s=>l.CancelDlg())},{default:n(()=>[m(f(o.__("Cancel")),1)]),_:1}),i(g,{class:N(["ms-2",{"opacity-25":e.form.processing}]),onClick:l.submit,disabled:e.form.processing},{default:n(()=>[m(f(o.__("Save")),1)]),_:1},8,["onClick","class","disabled"])])]),_:1},8,["show"])}const co=U(A,[["render",z]]);export{co as default};