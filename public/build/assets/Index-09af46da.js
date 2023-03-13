import{A as S}from"./AppLayout-1a839f95.js";import{C as j}from"./Confirm-41f29a5d.js";import N from"./Edit-a799b6f6.js";import{F as T}from"./inertiajs-tables-laravel-query-builder.es-5f1eef6b.js";import{J as $}from"./SecondaryButton-0e4a566a.js";import{J as A}from"./Button-c7c1269f.js";import{a as v,r as a,o as m,b as D,w as d,e as p,g as _,t as s,d as o,j as i,v as n,k as l,f as c,h as F,i as J,F as O,p as P,l as V}from"./app-a7c937a1.js";import{_ as q}from"./_plugin-vue_export-helper-c27b6911.js";import"./Edit-01bde3d2.js";import"./ActionMessage-86d2cefe.js";import"./ActionSection-96b34f83.js";import"./SectionTitle-57156eee.js";import"./ConfirmationModal-fc660ee4.js";import"./Modal-d0d07394.js";import"./DangerButton-cae1aa00.js";import"./DialogModal-9ccdd4ea.js";import"./FormSection-fba54185.js";import"./Input-fd3ff2e6.js";import"./Checkbox-f9be08cc.js";import"./InputError-147afb17.js";import"./Label-9c529059.js";import"./SectionBorder-790e6c81.js";import"./ValidationErrors-62fdbd60.js";import"./Edit-1ec525ac.js";import"./vue3-multiselect.umd.min-18b68693.js";/* empty css                                                                  */import"./Load-131c55d3.js";import"./Load-1a88fc17.js";import"./Edit-0cd1ee03.js";import"./Upload-c519c160.js";import"./Upload-feb55a03.js";import"./Settings-519853de.js";import"./Settings-4bb87828.js";import"./Upload-aed85c43.js";import"./Request-bff86671.js";import"./TextField-0eb10881.js";import"./Add-1c7e008b.js";import"./Load-051be2ee.js";import"./Upload-9760ab9e.js";import"./Load-47d081ba.js";import"./sweetalert.min-cf20c789.js";import"./ItemsMap-75bf1f86.js";import"./Upload-259c1194.js";import"./Upload-84201142.js";import"./ItemsMap-d1e6a413.js";import"./RequestEx-1ce14a12.js";const E={components:{AppLayout:S,Confirm:j,EditBranch:N,Table:T,SecondaryButton:$,JetButton:A},props:{branches:Object},data(){return{branch:Object,images:Object}},methods:{editBranch(e){this.branch=e,this.$nextTick(()=>this.$refs.dlg2.ShowDialog())},removeBranch(e){this.branch=e,this.$refs.dlg1.ShowModal()},remove(){v.delete(route("branches.destroy",{branch:this.branch.Id})).then(e=>{this.$store.dispatch("setSuccessFlashMessage",!0),setTimeout(()=>{window.location.reload()},500)}).catch(e=>{})},getImages(){const e=this.branches.data.map(t=>t.Id).join(",");v.get(`/getBranchesImages/${e}`).then(t=>{this.images=t.data}).catch(t=>console.error(t))}},mounted(){this.getImages()}},y=e=>(P("data-v-dbf29111"),e=e(),V(),e),L={class:"py-4"},M={class:"mx-auto sm:px-6 lg:px-8"},Q={class:"bg-white overflow-hidden shadow-xl sm:rounded-lg p-4"},R={style:{"text-align":"center"}},z={key:0},G=["src"],H={key:1},K=y(()=>o("i",{class:"fa fa-edit"},null,-1)),U=y(()=>o("i",{class:"fa fa-trash"},null,-1));function W(e,t,f,X,h,u){const g=a("edit-branch"),b=a("confirm"),B=a("secondary-button"),w=a("jet-button"),I=a("Table"),k=a("app-layout");return m(),D(k,null,{default:d(()=>[p(g,{ref:"dlg2",branch:h.branch},null,8,["branch"]),p(b,{ref:"dlg1",onConfirmed:t[0]||(t[0]=r=>u.remove())},{default:d(()=>[_(s(e.__("Are you sure you want to delete this branch?")),1)]),_:1},512),o("div",L,[o("div",M,[o("div",Q,[p(I,{filters:e.queryBuilderProps.filters,search:e.queryBuilderProps.search,columns:e.queryBuilderProps.columns,"on-update":e.setQueryBuilder,meta:f.branches},{head:d(()=>[o("tr",null,[i(o("th",{onClick:t[1]||(t[1]=l(r=>e.sortBy("Id"),["prevent"]))},s(e.__("ID")),513),[[n,e.show("Id")]]),i(o("th",{onClick:t[2]||(t[2]=l(r=>e.sortBy("name"),["prevent"]))},s(e.__("Name")),513),[[n,e.show("name")]]),i(o("th",{onClick:t[3]||(t[3]=l(r=>e.sortBy("receiver_id"),["prevent"]))},s(e.__("Registration Number")),513),[[n,e.show("receiver_id")]]),i(o("th",{onClick:t[4]||(t[4]=l(r=>e.sortBy("type"),["prevent"]))},s(e.__("Type(B|P)")),513),[[n,e.show("type")]]),o("th",R,s(e.__("Branch Logo")),1),o("th",{onClick:t[5]||(t[5]=l(()=>{},["prevent"]))},s(e.__("Actions")),1)])]),body:d(()=>[(m(!0),c(O,null,F(f.branches.data,r=>(m(),c("tr",{key:r.id},[i(o("td",null,s(r.Id),513),[[n,e.show("Id")]]),i(o("td",null,s(r.name),513),[[n,e.show("name")]]),i(o("td",null,s(r.receiver_id),513),[[n,e.show("receiver_id")]]),i(o("td",null,s(r.type=="B"?e.__("Business"):e.__("Individual")),513),[[n,e.show("type")]]),Object.keys(h.images).length>0?(m(),c("td",z,[h.images[r.Id]!="N/A"?(m(),c("img",{key:0,src:"/storage/"+h.images[r.Id],alt:"Branch Image",class:"object-cover"},null,8,G)):(m(),c("span",H,s(e.__("No Image")),1))])):J("",!0),o("td",null,[p(B,{onClick:C=>u.editBranch(r)},{default:d(()=>[K,_(" "+s(e.__("Edit")),1)]),_:2},1032,["onClick"]),p(w,{class:"ms-2",onClick:C=>u.removeBranch(r)},{default:d(()=>[U,_(" "+s(e.__("Delete")),1)]),_:2},1032,["onClick"])])]))),128))]),_:1},8,["filters","search","columns","on-update","meta"])])])])]),_:1})}const Re=q(E,[["render",W],["__scopeId","data-v-dbf29111"]]);export{Re as default};