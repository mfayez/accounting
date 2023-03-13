import{J as S}from"./ActionMessage-86d2cefe.js";import{J as L}from"./ActionSection-96b34f83.js";import{J as A}from"./Button-c7c1269f.js";import{J as P}from"./ConfirmationModal-fc660ee4.js";import{J as z}from"./DangerButton-cae1aa00.js";import{J as V}from"./DialogModal-9ccdd4ea.js";import{J as I}from"./FormSection-fba54185.js";import{J as N}from"./Input-fd3ff2e6.js";import{J as D}from"./InputError-147afb17.js";import{J as E}from"./Label-9c529059.js";import{J as O}from"./SecondaryButton-0e4a566a.js";import{J as U}from"./SectionBorder-790e6c81.js";import{_ as q}from"./_plugin-vue_export-helper-c27b6911.js";import{r as c,o as r,f as a,e as n,w as o,g as m,d as s,F as f,h as p,n as v,t as g,i as u}from"./app-a7c937a1.js";import"./SectionTitle-57156eee.js";import"./Modal-d0d07394.js";const G={components:{JetActionMessage:S,JetActionSection:L,JetButton:A,JetConfirmationModal:P,JetDangerButton:z,JetDialogModal:V,JetFormSection:I,JetInput:N,JetInputError:D,JetLabel:E,JetSecondaryButton:O,JetSectionBorder:U},props:["team","availableRoles","userPermissions"],data(){return{addTeamMemberForm:this.$inertia.form({email:"",role:null}),updateRoleForm:this.$inertia.form({role:null}),leaveTeamForm:this.$inertia.form(),removeTeamMemberForm:this.$inertia.form(),currentlyManagingRole:!1,managingRoleFor:null,confirmingLeavingTeam:!1,teamMemberBeingRemoved:null}},methods:{addTeamMember(){this.addTeamMemberForm.post(route("team-members.store",this.team),{errorBag:"addTeamMember",preserveScroll:!0,onSuccess:()=>this.addTeamMemberForm.reset()})},cancelTeamInvitation(_){this.$inertia.delete(route("team-invitations.destroy",_),{preserveScroll:!0})},manageRole(_){this.managingRoleFor=_,this.updateRoleForm.role=_.membership.role,this.currentlyManagingRole=!0},updateRole(){this.updateRoleForm.put(route("team-members.update",[this.team,this.managingRoleFor]),{preserveScroll:!0,onSuccess:()=>this.currentlyManagingRole=!1})},confirmLeavingTeam(){this.confirmingLeavingTeam=!0},leaveTeam(){this.leaveTeamForm.delete(route("team-members.destroy",[this.team,this.$page.props.user]))},confirmTeamMemberRemoval(_){this.teamMemberBeingRemoved=_},removeTeamMember(){this.removeTeamMemberForm.delete(route("team-members.destroy",[this.team,this.teamMemberBeingRemoved]),{errorBag:"removeTeamMember",preserveScroll:!0,preserveState:!0,onSuccess:()=>this.teamMemberBeingRemoved=null})},displayableRole(_){return this.availableRoles.find(l=>l.key===_).name}}},H={key:0},K=s("div",{class:"col-span-6"},[s("div",{class:"max-w-xl text-sm text-gray-600"}," Please provide the email address of the person you would like to add to this team. ")],-1),Q={class:"col-span-6 sm:col-span-4"},W={key:0,class:"col-span-6 lg:col-span-4"},X={class:"relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"},Y=["onClick"],Z={class:"flex items-center"},$={key:0,class:"ms-2 h-5 w-5 text-green-400",fill:"none","stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",stroke:"currentColor",viewBox:"0 0 24 24"},ee=s("path",{d:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),te=[ee],oe={class:"mt-2 text-xs text-gray-600"},se={key:1},ne={class:"space-y-6"},re={class:"text-gray-600"},ae={class:"flex items-center"},me=["onClick"],le={key:2},ie={class:"space-y-6"},ce={class:"flex items-center"},de=["src","alt"],ue={class:"ms-4"},_e={class:"flex items-center"},be=["onClick"],ve={key:1,class:"ms-2 text-sm text-gray-400"},ge=["onClick"],fe={key:0},pe={class:"relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer"},he=["onClick"],ye={class:"flex items-center"},ke={key:0,class:"ms-2 h-5 w-5 text-green-400",fill:"none","stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",stroke:"currentColor",viewBox:"0 0 24 24"},Te=s("path",{d:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"},null,-1),Me=[Te],Re={class:"mt-2 text-xs text-gray-600"};function xe(_,l,i,Fe,t,d){const h=c("jet-section-border"),k=c("jet-label"),C=c("jet-input"),T=c("jet-input-error"),j=c("jet-action-message"),M=c("jet-button"),w=c("jet-form-section"),R=c("jet-action-section"),y=c("jet-secondary-button"),J=c("jet-dialog-modal"),x=c("jet-danger-button"),F=c("jet-confirmation-modal");return r(),a("div",null,[i.userPermissions.canAddTeamMembers?(r(),a("div",H,[n(h),n(w,{onSubmitted:d.addTeamMember},{title:o(()=>[m(" Add Team Member ")]),description:o(()=>[m(" Add a new team member to your team, allowing them to collaborate with you. ")]),form:o(()=>[K,s("div",Q,[n(k,{for:"email",value:"Email"}),n(C,{id:"email",type:"email",class:"mt-1 block w-full",modelValue:t.addTeamMemberForm.email,"onUpdate:modelValue":l[0]||(l[0]=e=>t.addTeamMemberForm.email=e)},null,8,["modelValue"]),n(T,{message:t.addTeamMemberForm.errors.email,class:"mt-2"},null,8,["message"])]),i.availableRoles.length>0?(r(),a("div",W,[n(k,{for:"roles",value:"Role"}),n(T,{message:t.addTeamMemberForm.errors.role,class:"mt-2"},null,8,["message"]),s("div",X,[(r(!0),a(f,null,p(i.availableRoles,(e,b)=>(r(),a("button",{type:"button",class:v(["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200",{"border-t border-gray-200 rounded-t-none":b>0,"rounded-b-none":b!=Object.keys(i.availableRoles).length-1}]),onClick:B=>t.addTeamMemberForm.role=e.key,key:e.key},[s("div",{class:v({"opacity-50":t.addTeamMemberForm.role&&t.addTeamMemberForm.role!=e.key})},[s("div",Z,[s("div",{class:v(["text-sm text-gray-600",{"font-semibold":t.addTeamMemberForm.role==e.key}])},g(e.name),3),t.addTeamMemberForm.role==e.key?(r(),a("svg",$,te)):u("",!0)]),s("div",oe,g(e.description),1)],2)],10,Y))),128))])])):u("",!0)]),actions:o(()=>[n(j,{on:t.addTeamMemberForm.recentlySuccessful,class:"mr-3"},{default:o(()=>[m(" Added. ")]),_:1},8,["on"]),n(M,{class:v({"opacity-25":t.addTeamMemberForm.processing}),disabled:t.addTeamMemberForm.processing},{default:o(()=>[m(" Add ")]),_:1},8,["class","disabled"])]),_:1},8,["onSubmitted"])])):u("",!0),i.team.team_invitations.length>0&&i.userPermissions.canAddTeamMembers?(r(),a("div",se,[n(h),n(R,{class:"mt-10 sm:mt-0"},{title:o(()=>[m(" Pending Team Invitations ")]),description:o(()=>[m(" These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation. ")]),content:o(()=>[s("div",ne,[(r(!0),a(f,null,p(i.team.team_invitations,e=>(r(),a("div",{class:"flex items-center justify-between",key:e.id},[s("div",re,g(e.email),1),s("div",ae,[i.userPermissions.canRemoveTeamMembers?(r(),a("button",{key:0,class:"cursor-pointer ms-6 text-sm text-red-500 focus:outline-none",onClick:b=>d.cancelTeamInvitation(e)}," Cancel ",8,me)):u("",!0)])]))),128))])]),_:1})])):u("",!0),i.team.users.length>0?(r(),a("div",le,[n(h),n(R,{class:"mt-10 sm:mt-0"},{title:o(()=>[m(" Team Members ")]),description:o(()=>[m(" All of the people that are part of this team. ")]),content:o(()=>[s("div",ie,[(r(!0),a(f,null,p(i.team.users,e=>(r(),a("div",{class:"flex items-center justify-between",key:e.id},[s("div",ce,[s("img",{class:"w-8 h-8 rounded-full",src:e.profile_photo_url,alt:e.name},null,8,de),s("div",ue,g(e.name),1)]),s("div",_e,[i.userPermissions.canAddTeamMembers&&i.availableRoles.length?(r(),a("button",{key:0,class:"ms-2 text-sm text-gray-400 underline",onClick:b=>d.manageRole(e)},g(d.displayableRole(e.membership.role)),9,be)):i.availableRoles.length?(r(),a("div",ve,g(d.displayableRole(e.membership.role)),1)):u("",!0),_.$page.props.user.id===e.id?(r(),a("button",{key:2,class:"cursor-pointer ms-6 text-sm text-red-500",onClick:l[1]||(l[1]=(...b)=>d.confirmLeavingTeam&&d.confirmLeavingTeam(...b))}," Leave ")):u("",!0),i.userPermissions.canRemoveTeamMembers?(r(),a("button",{key:3,class:"cursor-pointer ms-6 text-sm text-red-500",onClick:b=>d.confirmTeamMemberRemoval(e)}," Remove ",8,ge)):u("",!0)])]))),128))])]),_:1})])):u("",!0),n(J,{show:t.currentlyManagingRole,onClose:l[3]||(l[3]=e=>t.currentlyManagingRole=!1)},{title:o(()=>[m(" Manage Role ")]),content:o(()=>[t.managingRoleFor?(r(),a("div",fe,[s("div",pe,[(r(!0),a(f,null,p(i.availableRoles,(e,b)=>(r(),a("button",{type:"button",class:v(["relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200",{"border-t border-gray-200 rounded-t-none":b>0,"rounded-b-none":b!==Object.keys(i.availableRoles).length-1}]),onClick:B=>t.updateRoleForm.role=e.key,key:e.key},[s("div",{class:v({"opacity-50":t.updateRoleForm.role&&t.updateRoleForm.role!==e.key})},[s("div",ye,[s("div",{class:v(["text-sm text-gray-600",{"font-semibold":t.updateRoleForm.role===e.key}])},g(e.name),3),t.updateRoleForm.role===e.key?(r(),a("svg",ke,Me)):u("",!0)]),s("div",Re,g(e.description),1)],2)],10,he))),128))])])):u("",!0)]),footer:o(()=>[n(y,{onClick:l[2]||(l[2]=e=>t.currentlyManagingRole=!1)},{default:o(()=>[m(" Cancel ")]),_:1}),n(M,{class:v(["ms-2",{"opacity-25":t.updateRoleForm.processing}]),onClick:d.updateRole,disabled:t.updateRoleForm.processing},{default:o(()=>[m(" Save ")]),_:1},8,["onClick","class","disabled"])]),_:1},8,["show"]),n(F,{show:t.confirmingLeavingTeam,onClose:l[5]||(l[5]=e=>t.confirmingLeavingTeam=!1)},{title:o(()=>[m(" Leave Team ")]),content:o(()=>[m(" Are you sure you would like to leave this team? ")]),footer:o(()=>[n(y,{onClick:l[4]||(l[4]=e=>t.confirmingLeavingTeam=!1)},{default:o(()=>[m(" Cancel ")]),_:1}),n(x,{class:v(["ms-2",{"opacity-25":t.leaveTeamForm.processing}]),onClick:d.leaveTeam,disabled:t.leaveTeamForm.processing},{default:o(()=>[m(" Leave ")]),_:1},8,["onClick","class","disabled"])]),_:1},8,["show"]),n(F,{show:t.teamMemberBeingRemoved,onClose:l[7]||(l[7]=e=>t.teamMemberBeingRemoved=null)},{title:o(()=>[m(" Remove Team Member ")]),content:o(()=>[m(" Are you sure you would like to remove this person from the team? ")]),footer:o(()=>[n(y,{onClick:l[6]||(l[6]=e=>t.teamMemberBeingRemoved=null)},{default:o(()=>[m(" Cancel ")]),_:1}),n(x,{class:v(["ms-2",{"opacity-25":t.removeTeamMemberForm.processing}]),onClick:d.removeTeamMember,disabled:t.removeTeamMemberForm.processing},{default:o(()=>[m(" Remove ")]),_:1},8,["onClick","class","disabled"])]),_:1},8,["show"])])}const Ue=q(G,[["render",xe]]);export{Ue as default};