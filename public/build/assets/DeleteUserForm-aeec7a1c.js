import{J as _}from"./ActionSection-96b34f83.js";import{J as y}from"./DialogModal-9ccdd4ea.js";import{J as w}from"./DangerButton-cae1aa00.js";import{J as h}from"./Input-fd3ff2e6.js";import{J as g}from"./InputError-147afb17.js";import{J as D}from"./SecondaryButton-0e4a566a.js";import{r as s,o as j,b as C,w as e,g as o,d as l,e as r,X as J,n as U}from"./app-a7c937a1.js";import{_ as b}from"./_plugin-vue_export-helper-c27b6911.js";import"./SectionTitle-57156eee.js";import"./Modal-d0d07394.js";const k={components:{JetActionSection:_,JetDangerButton:w,JetDialogModal:y,JetInput:h,JetInputError:g,JetSecondaryButton:D},data(){return{confirmingUserDeletion:!1,form:this.$inertia.form({password:""})}},methods:{confirmUserDeletion(){this.confirmingUserDeletion=!0,setTimeout(()=>this.$refs.password.focus(),250)},deleteUser(){this.form.delete(route("current-user.destroy"),{preserveScroll:!0,onSuccess:()=>this.closeModal(),onError:()=>this.$refs.password.focus(),onFinish:()=>this.form.reset()})},closeModal(){this.confirmingUserDeletion=!1,this.form.reset()}}},x=l("div",{class:"max-w-xl text-sm text-gray-600"}," Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain. ",-1),A={class:"mt-5"},B={class:"mt-4"};function V(v,a,M,S,t,n){const c=s("jet-danger-button"),i=s("jet-input"),d=s("jet-input-error"),m=s("jet-secondary-button"),u=s("jet-dialog-modal"),p=s("jet-action-section");return j(),C(p,null,{title:e(()=>[o(" Delete Account ")]),description:e(()=>[o(" Permanently delete your account. ")]),content:e(()=>[x,l("div",A,[r(c,{onClick:n.confirmUserDeletion},{default:e(()=>[o(" Delete Account ")]),_:1},8,["onClick"])]),r(u,{show:t.confirmingUserDeletion,onClose:n.closeModal},{title:e(()=>[o(" Delete Account ")]),content:e(()=>[o(" Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account. "),l("div",B,[r(i,{type:"password",class:"mt-1 block w-3/4",placeholder:"Password",ref:"password",modelValue:t.form.password,"onUpdate:modelValue":a[0]||(a[0]=f=>t.form.password=f),onKeyup:J(n.deleteUser,["enter"])},null,8,["modelValue","onKeyup"]),r(d,{message:t.form.errors.password,class:"mt-2"},null,8,["message"])])]),footer:e(()=>[r(m,{onClick:n.closeModal},{default:e(()=>[o(" Cancel ")]),_:1},8,["onClick"]),r(c,{class:U(["ms-2",{"opacity-25":t.form.processing}]),onClick:n.deleteUser,disabled:t.form.processing},{default:e(()=>[o(" Delete Account ")]),_:1},8,["onClick","class","disabled"])]),_:1},8,["show","onClose"])]),_:1})}const q=b(k,[["render",V]]);export{q as default};