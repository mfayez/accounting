<template>
    <jet-dialog-modal :show="showDialog" @close="showDialog = false">
        <template #title>
            {{ __("Customer Information") }}
        </template>

        <template #content>
            <jet-validation-errors class="mb-4" />

            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
					<div>
                        <div>
							<jet-label for="type" :value="__('Customer Type')" />
							<select
	                            id="type"
    	                        v-model="form.type"
        	                    class="mt-1 block w-full rounded border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
            	            >
                  	            <option value="P">{{ __("Person") }}</option>
                    	        <option value="B">{{ __("Business") }}</option>
                                <option value="F">{{ __("Foreign Customer") }}</option>
                        	</select>
                        </div>

                        <div class="mt-4">
                            <jet-label
                                for="id"
                                :value="__('Tax Registration Number')"
                            />
                            <jet-input
                                id="id"
                                type="number"
                                class="mt-1 block w-full"
                                v-model="form.receiver_id"
                                required
                            />
                        </div>
                        <div class="mt-4">
                            <jet-label for="name" :value="__('Customer Name')" />
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
                                for="customerNo"
                                :value="__('Internal Code')"
                            />
                            <jet-input
                                id="customerNo"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.code"
                                required
                                autofocus
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
                    </div>
                    <div>
                        <div>
                            <jet-label :value="__('Country')" />
                            <select
                                id="country"
                                v-model="form.address.country"
                                class="mt-1 block w-full rounded border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
                                @change="onCountryChange($event)"
                            >
                                <option
                                    v-for="country in countries"
                                    :value="country.code"
                                >
                                    {{ __(country.name) }}
                                </option>
                            </select>
                        </div>
                        
                        <div class="mt-4">
                            <jet-label
                                for="governate"
                                :value="__('Governate/State')"
                            />
							<select
	                            id="governate"
    	                        v-model="form.address.governate"
        	                    class="mt-1 block w-full rounded border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
            	            >
                               <option
                                    v-for="governate in states"
                                    :value="governate.name"
                                >
                                    {{ __(governate.name) }}
                                </option>
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
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.address.buildingNumber"
                                required
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex items-center justify-end">
                <jet-secondary-button @click="CancelAddcustomer()">
                    {{ __("Cancel") }}
                </jet-secondary-button>

                <jet-button
                    class="ms-2"
                    @click="submit"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ __("Save") }}
                </jet-button>
            </div>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetActionSection from "@/Jetstream/ActionSection";
import JetButton from "@/Jetstream/Button";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetCheckbox from "@/Jetstream/Checkbox";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import JetValidationErrors from "@/Jetstream/ValidationErrors";

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
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        JetValidationErrors,
    },

    props: {
        customer: {
            Type: Object,
            default: null,
        },
    },

    data() {
        return {
            errors: [],
            countries: [
                { name: "Egypt", code: "EG" },
                { name: "United States", code: "US" },
                { name: "United Kingdom", code: "UK" },
            ],
            states: [],
            allStates: [],
            form: this.$inertia.form({
                name: "",
                receiver_id: "",
                type: "B",
				code: "",
                address: {
                    customerID: "",
                    country: "EG",
                    governate: "",
                    regionCity: "",
                    street: "",
                    buildingNumber: "",
                    postalCode: "",
                    additionalInformation: "",
                },
            }),
            showDialog: false,
        };
    },

    methods: {
        ShowDialog() {
            if (this.customer !== null) {
                this.form.name = this.customer.name;
                this.form.receiver_id = this.customer.receiver_id;
                this.form.type = this.customer.type;
                this.form.code = this.customer.code;
                this.form.address.customerID = this.customer.address.customerID;
                this.form.address.country = this.customer.address.country;
                this.allStates.find((state) => {
                    if (state.countryShortCode == this.form.address.country) {
                        this.states = state.regions;
                    }
                });
                this.form.address.governate = this.customer.address.governate;
                this.form.address.regionCity = this.customer.address.regionCity;
                this.form.address.street = this.customer.address.street;
                this.form.address.buildingNumber =
                    this.customer.address.buildingNumber;
                this.form.address.postalCode = this.customer.address.postalCode;
                this.form.address.additionalInformation =
                    this.customer.address.additionalInformation;
            }
            this.showDialog = true;
        },
        CancelAddcustomer() {
            this.showDialog = false;
        },
        SaveCustomer() {
            axios
                .put(
                    route("customers.update", { customer: this.customer.Id }),
                    this.form
                )
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.showDialog = false;
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .catch((error) => {
                    console.log(error);
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors; //.password[0];
                    //this.$refs.password.focus()
                });
        },
        SaveNewCustomer() {
            axios
                .post(route("customers.store"), this.form)
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.processing = false;
                    this.$nextTick(() => this.$emit("dataUpdated"));
                    this.form.reset();
                    this.form.processing = false;
                    this.showDialog = false;
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                })
                .catch((error) => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors; //.password[0];
                    //this.$refs.password.focus()
                });
        },
        submit() {
            if (this.customer == null) this.SaveNewCustomer();
            else this.SaveCustomer();
        },
        onCountryChange(event){
            alert(event.target.value);
            this.allStates.find((state) => {
                if (state.countryShortCode == event.target.value) {
                    this.states = state.regions;
                }
            });
            console.log(this.states);
        }
    },
    created: function created() {
        axios
            .get("/json/Countries.json")
            .then((response) => {
                this.countries = response.data.map((country) => {
                    return {
                        name: country.countryName,
                        code: country.countryShortCode,
                    };
                });
                this.allStates = response.data;
                this.allStates.find((state) => {
                    if (state.countryShortCode == this.form.address.country) {
                        this.states = state.regions;
                    }
                });
            })
            .catch((error) => {});
    },
};
</script>
