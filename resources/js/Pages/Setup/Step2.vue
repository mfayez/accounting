<template>
    <div class="px-3 py-6 w-screen h-screen flex justify-center items-center">
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
                    <h1 class="text-2xl font-semibold text-gray-700">الخطوة الثانية</h1>
                    يجب أدخال بيانات الشركة
                </div>
            <form @submit.prevent="submit" dir="rtl">
                <div class="mt-4">
                    <label
                        class="block mb-2 text-sm font-medium text-gray-600"
                        for="LoggingEmailAddress"
                        >أسم الشركة</label
                    >
                    <input
                        id="client_id"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="text"
                        v-model="form.company_name"
                    />
                    <input-error :message="form.errors.company_name" />
                </div>
                <div class="mt-4">
                    <label
                        class="block mb-2 text-sm font-medium text-gray-600"
                        for="LoggingEmailAddress"
                        >رقم التسجيل الضريبي</label
                    >
                    <input
                        id="LoggingEmailAddress"
                        class="block w-full px-4 py-2 text-gray-700 bg-white border rounded-md focus:outline-none focus:border-black"
                        type="text"
                        v-model="form.tax_number"
                    />
                    <input-error :message="form.errors.tax_number" />
                </div>

                <div class="mt-8 flex justify justify-between">
                    <button
                        class="w-1/3 px-4 py-2 my-4 tracking-wide text-white transition-colors duration-200 transform bg-gray-700 rounded hover:bg-gray-600 focus:outline-none focus:bg-gray-600"
                        :disabled="formIsProcessing"
                        @click="goBack"
                    >
                        {{ "السابق" }}
                    </button>

                    <button
                        class="w-1/3 px-4 py-2 my-4 tracking-wide text-white transition-colors duration-200 transform bg-gray-700 rounded hover:bg-gray-600 focus:outline-none focus:bg-gray-600"
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
import InputError from "@/Jetstream/InputError";
import swal from "sweetalert";

export default {
    components: {
        InputError,
    },
    props: {},
    data() {
        return {
            form: this.$inertia.form({
                type: 'Company Settings',
                company_name: "",
                tax_number: "",
            }),
        };
    },
    methods: {
        submit() {
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
                        this.$inertia.visit(route("setup.step3"));
                    });
                    
                }).catch(error => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                });
        },
        goBack() {
            window.location.href = route("setup.step1");
        },
    },
    computed: {
        formIsProcessing() {
            return this.form.processing;
        },
    },
    created() {
        this.form.reset("type", "Company Settings");
        axios.get(route('settings.json'), { params: { type: this.form.type} })
            .then(response => {
                this.form.company_name = response.data.company_name;
                this.form.tax_number = response.data.tax_number;                
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
