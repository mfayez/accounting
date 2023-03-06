import{J as c}from"./InputError-147afb17.js";import{r as u,o as f,f as p,d as e,k as g,j as d,m as a,e as m,t as _,p as b,l as x}from"./app-a7c937a1.js";import{_ as h}from"./_plugin-vue_export-helper-c27b6911.js";const w={components:{InputError:c},props:{},data(){return{form:this.$inertia.form({email:"",password:"",remember:!1})}},methods:{submit(){this.form.clearErrors(),this.form.transform(t=>({...t,remember:this.form.remember?"on":""})).post(this.route("login"),{onFinish:()=>this.form.reset("password")})}},computed:{formIsProcessing(){return this.form.processing}}},n=t=>(b("data-v-5353536d"),t=t(),x(),t),y={class:"px-3 py-6 w-screen h-screen flex justify-center items-center"},v={class:"flex w-full mx-auto overflow-hidden bg-white rounded-lg shadow-xl lg:w-1/3"},k={class:"w-full px-6 py-8 md:px-8"},I=n(()=>e("h2",{class:"text-2xl font-semibold text-center text-gray-700"}," Accounting Master ",-1)),E=n(()=>e("p",{class:"text-xl text-center text-gray-600"},"Welcome back!",-1)),P={class:"mt-4"},L=n(()=>e("label",{class:"block mb-2 text-sm font-medium text-gray-600",for:"LoggingEmailAddress"},"Email Address",-1)),S={class:"mt-4"},A=n(()=>e("div",{class:"flex justify-between"},[e("label",{class:"block mb-2 text-sm font-medium text-gray-600",for:"loggingPassword"},"Password"),e("a",{href:"#",class:"text-xs text-gray-500 hover:underline"},"Forget Password?")],-1)),V={class:"mt-8"},j=["disabled"];function B(t,s,M,C,r,i){const l=u("input-error");return f(),p("div",y,[e("div",v,[e("div",k,[I,E,e("form",{onSubmit:s[3]||(s[3]=g((...o)=>i.submit&&i.submit(...o),["prevent"]))},[e("div",P,[L,d(e("input",{id:"LoggingEmailAddress",class:"block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black",type:"email","onUpdate:modelValue":s[0]||(s[0]=o=>r.form.email=o)},null,512),[[a,r.form.email]]),m(l,{message:r.form.errors.email},null,8,["message"])]),e("div",S,[A,d(e("input",{id:"loggingPassword",class:"block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black",type:"password","onUpdate:modelValue":s[1]||(s[1]=o=>r.form.password=o)},null,512),[[a,r.form.password]]),m(l,{message:r.form.errors.password},null,8,["message"])]),e("div",V,[e("button",{class:"w-full px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-gray-700 rounded hover:bg-gray-600 focus:outline-none focus:bg-gray-600",disabled:i.formIsProcessing,onClick:s[2]||(s[2]=(...o)=>i.submit&&i.submit(...o))},_(i.formIsProcessing?"Loading !":"Login"),9,j)])],32)])])])}const N=h(w,[["render",B],["__scopeId","data-v-5353536d"]]);export{N as default};
