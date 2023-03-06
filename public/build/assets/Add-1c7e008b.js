import{J as b}from"./ActionMessage-86d2cefe.js";import{J as y}from"./ActionSection-96b34f83.js";import{J as g}from"./Button-c7c1269f.js";import{J as V}from"./ConfirmationModal-fc660ee4.js";import{J}from"./DangerButton-cae1aa00.js";import{J as C}from"./DialogModal-9ccdd4ea.js";import{J as k}from"./FormSection-fba54185.js";import{J as L}from"./Input-fd3ff2e6.js";import{J as S}from"./Checkbox-f9be08cc.js";import{J as T}from"./InputError-147afb17.js";import{J as w}from"./Label-9c529059.js";import{J as j}from"./SecondaryButton-0e4a566a.js";import{J as D}from"./SectionBorder-790e6c81.js";import{J as B}from"./ValidationErrors-62fdbd60.js";import{M as U}from"./vue3-multiselect.umd.min-18b68693.js";import{T as M}from"./TextField-0eb10881.js";import{r as m,o as N,b as A,w as a,g as p,t as u,d as r,e as i,i as F,k as x,n as I}from"./app-a7c937a1.js";/* empty css                                                                  */import{_ as E}from"./_plugin-vue_export-helper-c27b6911.js";import"./SectionTitle-57156eee.js";import"./Modal-d0d07394.js";const O={components:{TextField:M,JetActionMessage:b,JetActionSection:y,JetButton:g,JetConfirmationModal:V,JetDangerButton:J,JetDialogModal:C,JetFormSection:k,JetInput:L,JetCheckbox:S,JetInputError:T,JetLabel:w,JetSecondaryButton:j,JetSectionBorder:D,JetValidationErrors:B,Multiselect:U},props:{positem:{Type:Object,default:null}},data(){return{errors:[],branches:[],activities:[],form:this.$inertia.form({name:"POS1",serial:"253142",os_version:"os",model:"Legacy_Model",pos_key:" ",grant_type:"client_credentials",client_id:"779bf63a-e6a3-452d-a7c9-09b80a726b14",client_secret:"20872ee3-f132-4954-8bf5-7d836a751f14",issuer_id:"",issuer:"",activity_code:"",activityCode:""}),showDialog:!1}},methods:{ShowDialog(){this.positem!==null&&(this.form.id=this.positem.id,this.form.name=this.positem.name,this.form.serial=this.positem.serial,this.form.os_version=this.positem.os_version,this.form.model=this.positem.model,this.form.pos_key=this.positem.pos_key,this.form.activity_code=this.positem.activity_code,this.form.grant_type=this.positem.grant_type,this.form.client_id=this.positem.client_id,this.form.client_secret=this.positem.client_secret,this.form.issuer_id=this.positem.issuer_id,this.form.issuer=this.branches.find(t=>t.Id===this.positem.issuer_id),this.form.activityCode=this.activities.find(t=>t.code===this.positem.activity_code)),this.showDialog=!0},CancelAddRequest(){this.form.reset(),this.showDialog=!1},submit(){this.form.issuer_id=this.form.issuer.Id,this.form.activity_code=this.form.activityCode.code,this.positem!==null?axios.put(route("pos.update",this.positem.id),this.form).then(t=>{this.$store.dispatch("setSuccessFlashMessage",!0),this.$nextTick(()=>this.$emit("dataUpdated")),this.form.reset(),this.form.processing=!1,this.showDialog=!1}).catch(t=>{this.form.processing=!1,this.$page.props.errors=t.response.data.errors,this.errors=t.response.data.errors}):axios.post(route("pos.store"),this.form).then(t=>{this.$store.dispatch("setSuccessFlashMessage",!0),this.$nextTick(()=>this.$emit("dataUpdated")),this.form.reset(),this.form.processing=!1,this.showDialog=!1}).catch(t=>{this.form.processing=!1,this.$page.props.errors=t.response.data.errors,this.errors=t.response.data.errors})}},created:function(){axios.get(route("json.branches")).then(e=>{this.branches=e.data,this.positem&&(this.form.issuer=this.branches.find(n=>n.Id===this.positem.issuer_id))}).catch(e=>{}),axios.get("/json/ActivityCodes.json").then(e=>{this.activities=e.data,this.positem?this.form.activityCode=this.activities.find(n=>n.code===this.positem.activity_code):this.form.activityCode=this.activities[0]}).catch(e=>{})}},P={class:"grid grid-cols-2 gap-4"};const q={class:"col-span-2"},R={class:"col-span-2"},z={class:"lg:col-span-2"},G={class:"flex items-center justify-end"};function K(t,e,n,H,o,d){const l=m("TextField"),c=m("jet-label"),f=m("multiselect"),h=m("jet-secondary-button"),_=m("jet-button"),v=m("jet-dialog-modal");return N(),A(v,{show:o.showDialog,onClose:e[12]||(e[12]=s=>o.showDialog=!1)},{title:a(()=>[p(u(t.__("Add New POS")),1)]),content:a(()=>[r("form",{onSubmit:e[10]||(e[10]=x((...s)=>d.submit&&d.submit(...s),["prevent"]))},[r("div",P,[r("div",null,[i(l,{modelValue:o.form.name,"onUpdate:modelValue":e[0]||(e[0]=s=>o.form.name=s),itemType:"input",itemLabel:t.__("POS Name")},null,8,["modelValue","itemLabel"])]),r("div",null,[i(c,{value:t.__("Branch")},null,8,["value"]),i(f,{modelValue:o.form.issuer,"onUpdate:modelValue":e[1]||(e[1]=s=>o.form.issuer=s),label:"name",options:o.branches,placeholder:"Select branch"},null,8,["modelValue","options"])]),r("div",null,[i(l,{modelValue:o.form.serial,"onUpdate:modelValue":e[2]||(e[2]=s=>o.form.serial=s),itemType:"input",itemLabel:t.__("Serial Number")},null,8,["modelValue","itemLabel"])]),r("div",null,[i(l,{modelValue:o.form.os_version,"onUpdate:modelValue":e[3]||(e[3]=s=>o.form.os_version=s),itemType:"input",itemLabel:t.__("Version")},null,8,["modelValue","itemLabel"])]),r("div",null,[i(l,{modelValue:o.form.model,"onUpdate:modelValue":e[4]||(e[4]=s=>o.form.model=s),itemType:"input",itemLabel:t.__("Model")},null,8,["modelValue","itemLabel"])]),r("div",null,[i(l,{modelValue:o.form.pos_key,"onUpdate:modelValue":e[5]||(e[5]=s=>o.form.pos_key=s),itemType:"input",itemLabel:t.__("POS Key")},null,8,["modelValue","itemLabel"])]),F("",!0),r("div",q,[i(l,{modelValue:o.form.client_id,"onUpdate:modelValue":e[7]||(e[7]=s=>o.form.client_id=s),itemType:"input",itemLabel:t.__("Client ID")},null,8,["modelValue","itemLabel"])]),r("div",R,[i(l,{modelValue:o.form.client_secret,"onUpdate:modelValue":e[8]||(e[8]=s=>o.form.client_secret=s),itemType:"input",itemLabel:t.__("Client Secret")},null,8,["modelValue","itemLabel"])]),r("div",z,[i(c,{value:t.__("Branch Activity")},null,8,["value"]),i(f,{modelValue:o.form.activityCode,"onUpdate:modelValue":e[9]||(e[9]=s=>o.form.activityCode=s),label:"Desc_ar",options:o.activities,placeholder:"Select activity"},null,8,["modelValue","options"])])])],32)]),footer:a(()=>[r("div",G,[i(h,{onClick:e[11]||(e[11]=s=>d.CancelAddRequest())},{default:a(()=>[p(u(t.__("Cancel")),1)]),_:1}),i(_,{class:I(["ms-2",{"opacity-25":o.form.processing}]),onClick:d.submit,disabled:o.form.processing},{default:a(()=>[p(u(t.__("Save")),1)]),_:1},8,["onClick","class","disabled"])])]),_:1},8,["show"])}const _e=E(O,[["render",K]]);export{_e as default};
