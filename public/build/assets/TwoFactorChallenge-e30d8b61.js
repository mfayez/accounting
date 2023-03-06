import{J as h}from"./AuthenticationCard-658fd9e9.js";import{J as b}from"./AuthenticationCardLogo-26430b3f.js";import{J as k}from"./Button-c7c1269f.js";import{J as x}from"./Input-fd3ff2e6.js";import{J as j}from"./Label-9c529059.js";import{J}from"./ValidationErrors-62fdbd60.js";import{r as s,o as r,b as V,w as d,e as c,d as l,f as n,g as i,F as m,k as y,n as C}from"./app-a7c937a1.js";import{_ as w}from"./_plugin-vue_export-helper-c27b6911.js";const B={components:{JetAuthenticationCard:h,JetAuthenticationCardLogo:b,JetButton:k,JetInput:x,JetLabel:j,JetValidationErrors:J},data(){return{recovery:!1,form:this.$inertia.form({code:"",recovery_code:""})}},methods:{toggleRecovery(){this.recovery^=!0,this.$nextTick(()=>{this.recovery?(this.$refs.recovery_code.focus(),this.form.code=""):(this.$refs.code.focus(),this.form.recovery_code="")})},submit(){this.form.post(this.route("two-factor.login"))}}},R={class:"mb-4 text-sm text-gray-600"},U={key:0},F={key:1},L={class:"flex items-center justify-end mt-4"};function N(T,o,A,E,e,a){const _=s("jet-authentication-card-logo"),p=s("jet-validation-errors"),u=s("jet-label"),f=s("jet-input"),v=s("jet-button"),g=s("jet-authentication-card");return r(),V(g,null,{logo:d(()=>[c(_)]),default:d(()=>[l("div",R,[e.recovery?(r(),n(m,{key:1},[i(" Please confirm access to your account by entering one of your emergency recovery codes. ")],64)):(r(),n(m,{key:0},[i(" Please confirm access to your account by entering the authentication code provided by your authenticator application. ")],64))]),c(p,{class:"mb-4"}),l("form",{onSubmit:o[3]||(o[3]=y((...t)=>a.submit&&a.submit(...t),["prevent"]))},[e.recovery?(r(),n("div",F,[c(u,{for:"recovery_code",value:"Recovery Code"}),c(f,{ref:"recovery_code",id:"recovery_code",type:"text",class:"mt-1 block w-full",modelValue:e.form.recovery_code,"onUpdate:modelValue":o[1]||(o[1]=t=>e.form.recovery_code=t),autocomplete:"one-time-code"},null,8,["modelValue"])])):(r(),n("div",U,[c(u,{for:"code",value:"Code"}),c(f,{ref:"code",id:"code",type:"text",inputmode:"numeric",class:"mt-1 block w-full",modelValue:e.form.code,"onUpdate:modelValue":o[0]||(o[0]=t=>e.form.code=t),autofocus:"",autocomplete:"one-time-code"},null,8,["modelValue"])])),l("div",L,[l("button",{type:"button",class:"text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer",onClick:o[2]||(o[2]=y((...t)=>a.toggleRecovery&&a.toggleRecovery(...t),["prevent"]))},[e.recovery?(r(),n(m,{key:1},[i(" Use an authentication code ")],64)):(r(),n(m,{key:0},[i(" Use a recovery code ")],64))]),c(v,{class:C(["ms-4",{"opacity-25":e.form.processing}]),disabled:e.form.processing},{default:d(()=>[i(" Log in ")]),_:1},8,["class","disabled"])])],32)]),_:1})}const H=w(B,[["render",N]]);export{H as default};
