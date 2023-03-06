<template>
    <div class="px-3 py-6 w-screen h-screen flex justify-center items-center">
        <jet-dialog-modal :show="showDialog" @close="showDialog=false" max-width="2xl" max-height="h-1/2"
        >
            <template #title>
                الفيديو للتعرف على طريقة الربط و الحصول على كود الربط
            </template>
            <template #content>
                <iframe
                    width="100%"
                    height="480px"
                    src="https://www.youtube.com/watch?v=L3dX9TpcHsU"
                    title="Accounting Master"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen />				
            </template>
        </jet-dialog-modal>
		<div
            class="grid grid-cols-2 gap-4 w-full lg:w-2/5 xl:w-2/5 2xl:w-2/6 border border-gray-200 rounded-lg shadow-lg p-6"
        >
            <div class="col-span-2 items-center justify-center h-full p-12">
                <div class="w-full">
                    <h1 class="text-center text-2xl font-semibold text-gray-700">مرحباً فى برنامج انفويس ماستر</h1>
                    <p class="text-center mt-2 text-sm text-gray-500">
                        ثلاث خطوات سهلة لأعداد البرنامج
                    </p>
                </div>
            </div>
            <div class="col-span-2 bg-cover lg:block">
                <div class="text-right">
                    <h1 class="text-2xl font-semibold text-gray-700">الخطوة الأولى</h1>
                    <h2 class="text-2xl text-gray-700">ربط البرنامج بمصلحة الضرائب</h2>
                    <a href="#" @click.prevent="showDialog = true">يمكنك مشاهدة الفيديو للتعرف على طريقة الربط و الحصول على كود الربط</a>
                </div>
            <form @submit.prevent="submit" dir="ltr">
                <div class="mt-4">
                    <select
                        id="production"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="text"
                        v-model="form.isProduction"
                    >
                        <option value="true">البيئة الفعلية</option>
                        <option value="false">البيئة التجريبية</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label
                        class="block mb-2 text-sm font-medium text-gray-600"
                        for="LoggingEmailAddress"
                        >ETA Client ID</label
                    >
                    <input
                        id="client_id"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="text"
                        v-model="form.client_id"
                    />
                    <input-error :message="form.errors.client_id" />
                </div>
                <div class="mt-4">
                    <label
                        class="block mb-2 text-sm font-medium text-gray-600"
                        for="LoggingEmailAddress"
                        >ETA Client Secret 1</label
                    >
                    <input
                        id="LoggingEmailAddress"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="text"
                        v-model="form.client_secret1"
                    />
                    <input-error :message="form.errors.client_secret1" />
                </div>
                <div class="mt-4">
                    <label
                        class="block mb-2 text-sm font-medium text-gray-600"
                        for="LoggingEmailAddress"
                        >ETA Client Secret 2</label
                    >
                    <input
                        id="LoggingEmailAddress"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="text"
                        v-model="form.client_secret2"
                    />
                    <input-error :message="form.errors.client_secret2" />
                </div>

                <div class="mt-8 justify justify-right">
                    <button
                        class="w-1/3 px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-gray-700 rounded hover:bg-gray-600 focus:outline-none focus:bg-gray-600"
                        :disabled="formIsProcessing"
                        @click="submit"
                    >
                        {{ formIsProcessing ? "جاري التحميل" : "التالي" }}
                    </button>
                </div>
			</form>
            </div>
        </div>
    </div>
</template>

<script>
import InputError from "@/Jetstream/InputError.vue";
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import swal from "sweetalert";

export default {
    components: {
        InputError,
        JetDialogModal
    },
    props: {},
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                type: 'ETA Settings',
                client_id: "",
                client_secret1: "",
                client_secret2: "",
                isProduction: true,
                login_url: "https://id.eta.gov.eg/connect/token",
                eta_url: "https://api.invoicing.eta.gov.eg/api/v1.0",
                preview_url: "https://invoicing.eta.gov.eg/print/documents/",
            }),
        };
    },
    methods: {
        submit() {
            if (this.form.isProduction) {
                this.form.login_url = "https://id.eta.gov.eg/connect/token";
                this.form.eta_url = "https://api.invoicing.eta.gov.eg/api/v1.0";
                this.form.preview_url = "https://invoicing.eta.gov.eg/print/documents/";
            } else {
                this.form.login_url = "https://id.preprod.eta.gov.eg/connect/token";
                this.form.eta_url = "https://api.preprod.invoicing.eta.gov.eg/api/v1.0";
                this.form.preview_url = "https://preprod.invoicing.eta.gov.eg/print/documents/";
            }
            this.form.processing = true;
            axios.post(route('setup.ping_eta'), this.form)
                .then(response => {
                    console.log(response);
                    axios.post(route('settings.store'), this.form)
                        .then(response => {
                            this.form.processing = false;
                            swal({
                                title: "تم الحفظ بنجاح",
                                text: "سيتم تحويلك للخطوة التالية",
                                icon: "success",
                                button: "موافق",
                            }).then(() => {
                                this.form.processing = false;
                                this.$inertia.visit(route("setup.step2"));
                            });
                            
                        }).catch(error => {
                            this.form.processing = false;
                            this.$page.props.errors = error.response.data.errors;
                            this.errors = error.response.data.errors;
                        });
                })
                .catch(error => {
                    //show swal مصلحة الضرائب غير متاحة حاليا
                    this.form.processing = false;
                    swal({
                        title: "خطأ",
                        text: "مصلحة الضرائب غير متاحة حاليا",
                        icon: "error",
                        button: "موافق",
                    });
                });
            
        },
    },
    computed: {
        formIsProcessing() {
            return this.form.processing;
        },
    },
    created() {
        this.form.reset("type", "ETA Settings");
        axios.get(route('settings.json'), { params: { type: this.form.type} })
            .then(response => {
                this.form.client_id = response.data.client_id;
                this.form.client_secret1 = response.data.client_secret1;
                this.form.client_secret2 = response.data.client_secret2;
                this.form.isProduction = response.data.isProduction;
                this.form.login_url = response.data.login_url;
                this.form.eta_url = response.data.eta_url;
            }).catch(error => {
            });
    },
};
</script>
<style scoped>
button:disabled {
    cursor: not-allowed;
}
input:focus {
    box-shadow: none;
}
</style>
