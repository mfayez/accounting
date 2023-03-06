import{J}from"./ActionMessage-86d2cefe.js";import{J as b}from"./ActionSection-96b34f83.js";import{J as v}from"./Button-c7c1269f.js";import{J as C}from"./ConfirmationModal-fc660ee4.js";import{J as w}from"./DangerButton-cae1aa00.js";import{J as V}from"./DialogModal-9ccdd4ea.js";import{J as j}from"./FormSection-fba54185.js";import{J as D}from"./Input-fd3ff2e6.js";import{J as k}from"./Checkbox-f9be08cc.js";import{J as y}from"./InputError-147afb17.js";import{J as S}from"./Label-9c529059.js";import{J as x}from"./SecondaryButton-0e4a566a.js";import{J as B}from"./SectionBorder-790e6c81.js";import{J as I}from"./ValidationErrors-62fdbd60.js";import{M}from"./vue3-multiselect.umd.min-18b68693.js";import{r as n,o as P,b as T,w as l,g as c,t as u,e as i,d as r,k as U,n as A}from"./app-a7c937a1.js";import{_ as E}from"./_plugin-vue_export-helper-c27b6911.js";import"./SectionTitle-57156eee.js";import"./Modal-d0d07394.js";const N={components:{JetActionMessage:J,JetActionSection:b,JetButton:v,JetConfirmationModal:C,JetDangerButton:w,JetDialogModal:V,JetFormSection:j,JetInput:D,JetCheckbox:k,JetInputError:y,JetLabel:S,JetSecondaryButton:x,JetSectionBorder:B,JetValidationErrors:I,Multiselect:M},props:{accounting_item:{Type:Object,default:null}},data(){return{errors:[],form:this.$inertia.form({id:"",parent_id:"",name:"",description:"",status:""}),parent_item:null,items:[],showDialog:!1}},methods:{ShowDialog(){this.accounting_item!==null&&(this.form.id=this.accounting_item.id,this.form.parent_id=this.accounting_item.parent_id,this.form.name=this.accounting_item.name,this.form.description=this.accounting_item.description,this.form.status=this.accounting_item.status),this.showDialog=!0,this.$nextTick(()=>{this.accounting_item!=null&&this.items!=null&&(this.parent_item=this.items.find(t=>t.itemCode===this.form.ETACode),this.updateParentItem())})},CancelDlg(){this.showDialog=!1},submit(){axios.post(route("accounting.chart.store"),this.form).then(t=>{this.$store.dispatch("setSuccessFlashMessage",!0),this.processing=!1,this.form.reset(),this.form.processing=!1,this.showDialog=!1,setTimeout(()=>{window.location.reload()},500)}).catch(t=>{this.form.processing=!1,this.$page.props.errors=t.response.data.errors,this.errors=t.response.data.errors})},nameWithCode({id:t,name:e}){return t+" - "+e},updateParentItem(){this.parent_item&&(this.form.parent_id=this.parent_item.id)}},created(){axios.get(route("accounting.chart.json")).then(t=>{this.items=t.data,this.accounting_item!=null&&(this.parent_item=this.items.find(e=>e.id===this.form.parent_id),this.updateParentItem())}).catch(t=>{})}},W={class:"grid grid-cols-2 gap-4"},F={class:"col-span-2"},z={class:"col-span-2"},L={class:"col-span-2"},O={class:"col-span-2"},q={class:"flex items-center justify-end"};function G(t,e,H,K,o,a){const p=n("jet-validation-errors"),m=n("jet-label"),d=n("jet-input"),f=n("multiselect"),_=n("jet-secondary-button"),h=n("jet-button"),g=n("jet-dialog-modal");return P(),T(g,{show:o.showDialog,maxWidth:"3xl",onClose:e[6]||(e[6]=s=>o.showDialog=!1)},{title:l(()=>[c(u(t.__("Accounting Chart Dialog")),1)]),content:l(()=>[i(p,{class:"mb-4"}),r("form",{onSubmit:e[4]||(e[4]=U((...s)=>a.submit&&a.submit(...s),["prevent"]))},[r("div",W,[r("div",F,[i(m,{value:t.__("Code")},null,8,["value"]),i(d,{id:"id",type:"text",class:"mt-1 block w-full",modelValue:o.form.id,"onUpdate:modelValue":e[0]||(e[0]=s=>o.form.id=s),disabled:!0},null,8,["modelValue"])]),r("div",z,[i(m,{value:t.__("Parent")},null,8,["value"]),i(f,{modelValue:o.parent_item,"onUpdate:modelValue":[e[1]||(e[1]=s=>o.parent_item=s),a.updateParentItem],options:o.items,"custom-label":a.nameWithCode,label:"name","track-by":"id",placeholder:t.__("Select item")},null,8,["modelValue","options","custom-label","onUpdate:modelValue","placeholder"])]),r("div",L,[i(m,{value:t.__("Name")},null,8,["value"]),i(d,{id:"name",type:"text",class:"mt-1 block w-full",modelValue:o.form.name,"onUpdate:modelValue":e[2]||(e[2]=s=>o.form.name=s)},null,8,["modelValue"])]),r("div",O,[i(m,{value:t.__("Description")},null,8,["value"]),i(d,{id:"description",type:"text",class:"mt-1 block w-full",modelValue:o.form.description,"onUpdate:modelValue":e[3]||(e[3]=s=>o.form.description=s)},null,8,["modelValue"])])])],32)]),footer:l(()=>[r("div",q,[i(_,{onClick:e[5]||(e[5]=s=>a.CancelDlg())},{default:l(()=>[c(u(t.__("Cancel")),1)]),_:1}),i(h,{class:A(["ms-2",{"opacity-25":o.form.processing}]),onClick:a.submit,disabled:o.form.processing},{default:l(()=>[c(u(t.__("Save")),1)]),_:1},8,["onClick","class","disabled"])])]),_:1},8,["show"])}const pt=E(N,[["render",G]]);export{pt as default};
