<template>
    <div class="px-3 py-6 w-screen h-screen flex justify-center items-center" dir="rtl">
		<div class="grid grid-cols-1 gap-4 w-full lg:w-2/5 xl:w-2/5 2xl:w-1/3 border border-gray-200 rounded-lg shadow-lg p-6">
            <div class="col-span-1 items-center justify-center h-full p-12">
                <div class="w-full">
                    <h1 class="text-center text-2xl font-semibold text-gray-700">مرحباً فى برنامج انفويس ماستر</h1>
                    <p class="text-center mt-2 text-sm text-gray-500">
                        ثلاث خطوات سهلة لأعداد البرنامج
                    </p>
                </div>
            </div>
            <div class="col-span-1 bg-cover lg:block">
                <div class="text-right">
                    <h1 class="text-2xl font-semibold text-gray-700">الخطوة الثالثة</h1>
                     يجب أدخال بيانات الفرع الرئيسي المسجل لدى مصلحة الضرائب
                </div>
            </div>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div>
                            <jet-label
                                for="governate"
                                :value="__('Governate/State')"
                            />
							<select
	                            id="governate"
    	                        v-model="form.address.governate"
        	                    class="mt-1 block w-full rounded border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
            	            >
                               <option value="Cairo">{{ __("Cairo") }}</option>
                               <option value="Giza">{{ __("Giza") }}</option>
                               <option value="Alexandria">{{ __("Alexandria") }}</option>
                               <option value="Gharbiya">{{ __("Gharbiya") }}</option>
                               <option value="Qalioubiya">{{ __("Qalioubiya") }}</option>
                               <option value="Assiut">{{ __("Assiut") }}</option>
                               <option value="Aswan">{{ __("Aswan") }}</option>
                               <option value="Beheira">{{ __("Beheira") }}</option>
                               <option value="Bani Suef">{{ __("Bani Suef") }}</option>
                               <option value="Daqahliya">{{ __("Daqahliya") }}</option>
                               <option value="Damietta">{{ __("Damietta") }}</option>
                               <option value="Fayyoum">{{ __("Fayyoum") }}</option>
                               <option value="Helwan">{{ __("Helwan") }}</option>
                               <option value="Ismailia">{{ __("Ismailia") }}</option>
                               <option value="Kafr El Sheikh">{{ __("Kafr El Sheikh") }}</option>
                               <option value="Luxor">{{ __("Luxor") }}</option>
                               <option value="Marsa Matrouh">{{ __("Marsa Matrouh") }}</option>
                               <option value="Minya">{{ __("Minya") }}</option>
                               <option value="Monofiya">{{ __("Monofiya") }}</option>
                               <option value="New Valley">{{ __("New Valley") }}</option>
                               <option value="North Sinai">{{ __("North Sinai") }}</option>
                               <option value="Port Said">{{ __("Port Said") }}</option>
                               <option value="Qena">{{ __("Qena") }}</option>
                               <option value="Red Sea">{{ __("Red Sea") }}</option>
                               <option value="Sharqiya">{{ __("Sharqiya") }}</option>
                               <option value="Sohag">{{ __("Sohag") }}</option>
                               <option value="South Sinai">{{ __("South Sinai") }}</option>
                               <option value="Suez">{{ __("Suez") }}</option>
                               <option value="Tanta">{{ __("Tanta") }}</option>
                        	</select>
                        </div>
                        <div class="mt-4">
                            <jet-label
                                for="regionCity"
                                :value="__('Region/City')"
                            />
                            <jet-input
                                id="regionCity"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.address.regionCity"
                                required
                            />
                        </div>
                        <div class="mt-4">
                            <jet-label for="street" :value="__('Street')" />
                            <jet-input
                                id="street"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.address.street"
                                required
                            />
                        </div>
                        <div class="mt-4">
                            <jet-label
                                for="buildingNumber"
                                :value="__('Building Number')"
                            />
                            <jet-input
                                id="buildingNumber"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.address.buildingNumber"
                                required
                            />
                        </div>
                    </div>
                    <div>
                        <div class="">
                            <jet-label for="name" :value="__('Branch Name')" />
                            <jet-input
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                            />
                        </div>
                        <div class="mt-4">
                            <jet-label
                                for="additionalInformation"
                                :value="__('Additional Information (Location)')"
                            />
                            <jet-input
                                id="additionalInformation"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.address.additionalInformation"
                            />
                        </div>
                        <div class="mt-4">
                            <jet-label
                                for="postalCode"
                                :value="__('Postal Code')"
                            />
                            <jet-input
                                id="postalCode"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.address.postalCode"
                            />
                        </div>
                        <div class="mt-4">
                            <jet-label
                                for="branchLogo"
                                :value="__('Branch Logo')"
                            ></jet-label>
                            <input
                                type="file"
                                class="mt-1 block w-full py-2"
                                required
                                accept=".jpg,.png,.jpeg"
                                @change="branchLogo($event)"
                            />
                        </div>
                    </div>
                </div>
            </form>
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
                    {{ formIsProcessing ? "جاري التحميل" : "أبداء" }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import InputError from "@/Jetstream/InputError.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetActionSection from "@/Jetstream/ActionSection.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import swal from "sweetalert";

export default {
    components: {
        JetActionMessage,
        JetActionSection,
        JetButton,
        JetConfirmationModal,
        JetDangerButton,
        JetDialogModal,
        JetFormSection,
        JetInput,
        JetCheckbox,
        JetInputError,
        InputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        JetValidationErrors,
    },
    props: {},
    data() {
        return {
            errors: [],
            old_id: "",
            form: this.$inertia.form({
                name: "",
                type: "B",
                issuer_id: "",
                address: {
                    branchID: "0",
                    country: "EG",
                    governate: "",
                    regionCity: "",
                    street: "",
                    buildingNumber: "",
                    postalCode: "",
                    additionalInformation: "",
                },
                branchLogo: "",
            }),
        };
    },
    methods: {
        submit() {
            const form = new FormData();
            if (this.form.branchLogo) {
                form.append("branchLogo", this.form.branchLogo);
            }
            form.append("name", this.form.name);
            form.append("issuer_id", this.form.issuer_id);
            form.append("type", this.form.type);
            form.append("address[branchID]", this.form.address.branchID);
            form.append("address[country]", this.form.address.country);
            form.append("address[governate]", this.form.address.governate);
            form.append("address[regionCity]", this.form.address.regionCity);
            form.append("address[street]", this.form.address.street);
            form.append(
                "address[buildingNumber]",
                this.form.address.buildingNumber
            );
            form.append("address[postalCode]", this.form.address.postalCode);
            form.append(
                "address[additionalInformation]",
                this.form.address.additionalInformation
            );

            if (this.old_id){
                form.append("_method", "PUT");
                axios
                    .post(route("branches.update", { branch: this.old_id }),
                        form,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                        }
                    )
                    .then((response) => {
                        this.form.processing = false;
                        swal({
                            title: "تم الحفظ بنجاح",
                            text: "سيتم تحويلك الى البرنامج الأن",
                            icon: "success",
                            button: "موافق",
                        }).then(() => {
                            this.form.processing = false;
                            this.$inertia.visit(route("dashboard"));
                        });
                    })
                    .catch((error) => {
                        this.form.processing = false;
                        this.$page.props.errors = error.response.data.errors;
                        this.errors = error.response.data.errors; //.password[0];
                        //this.$refs.password.focus()
                    });
            } else {
                axios
                    .post(route("branches.store"), form, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        this.form.processing = false;
                        swal({
                            title: "تم الحفظ بنجاح",
                            text: "سيتم تحويلك الى البرنامج الأن",
                            icon: "success",
                            button: "موافق",
                        }).then(() => {
                            this.form.processing = false;
                            this.$inertia.visit(route("dashboard"));
                        });
                    })
                    .catch((error) => {
                        this.form.processing = false;
                        this.$page.props.errors = error.response.data.errors;
                        this.errors = error.response.data.errors; //.password[0];
                        //this.$refs.password.focus()
                    });
            }
        },
        goBack() {
            window.location.href = route("setup.step2");
        },
        branchLogo(e) {
            this.form.branchLogo = e.target.files[0];
        },
    },
    computed: {
        formIsProcessing() {
            return this.form.processing;
        },
    },
    created() {
        axios.get(route('settings.json'), { params: { type: "Company Settings"} })
            .then(response => {
                this.form.name = response.data.company_name;
                this.form.issuer_id = response.data.tax_number;                
            }).catch(error => {
            });
        axios.get(route('json.branches'))
            .then(response => {
                this.old_id = response.data[0].Id;
                this.form.name = response.data[0].name;
                this.form.issuer_id = response.data[0].issuer_id;
                this.form.address.branchID = response.data[0].address.branchID;
                this.form.address.country = response.data[0].address.country;
                this.form.address.governate = response.data[0].address.governate;
                this.form.address.regionCity = response.data[0].address.regionCity;
                this.form.address.street = response.data[0].address.street;
                this.form.address.buildingNumber =
                    response.data[0].address.buildingNumber;
                this.form.address.postalCode = response.data[0].address.postalCode;
                this.form.address.additionalInformation =
                    response.data[0].address.additionalInformation;
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
