<template>
    <jet-dialog-modal
        :show="showDialog"
        @close="showDialog = false"
        maxWidth="3xl"
    >
        <template #title>
            {{ __("Customer Information") }}
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <jet-label for="type" :value="__('Customer Type')" />
                        <select
                            id="type"
                            v-model="form.type"
                            class="mt-1 block w-full rounded border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm"
                        >
                            <option value="P">{{ __("Person") }}</option>
                            <option value="B">{{ __("Business") }}</option>
                        </select>
                        <input-error :message="form.errors.type" />
                    </div>
                    <div>
                        <jet-label
                            for="id"
                            :value="__('Tax Registration Number')"
                        />
                        <jet-input
                            id="id"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.receiver_id"
                            required
                        />
                        <input-error :message="form.errors.receiver_id" />
                    </div>
                    <div>
                        <jet-label for="name" :value="__('Name')" />
                        <jet-input
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                        />
                        <input-error :message="form.errors.name" />
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex items-center justify-end">
                <jet-secondary-button @click="CancelAddBranch()">
                    {{ __("Cancel") }}
                </jet-secondary-button>
				<jet-validation-errors class="mb-4" />
    	        <jet-button
        	    	class="ms-2"
            	    @click="submit"
                	:class="{ 'opacity-25': form.processing }"
	                :disabled="form.processing"
    	        >
            		{{ form.processing ? __("Loading !") : __("Save") }}
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
import Multiselect from "@suadelabs/vue3-multiselect";
import InputError from "@/Jetstream/InputError";

export default {
    components: {
        Multiselect,
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
        InputError,
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
            form: this.$inertia.form({
                name: "",
                receiver_id: "",
                type: "B",
				code: ''
            }),
            showDialog: false,
        };
    },
    methods: {
        ShowDialog() {
            if (this.customer !== null) {
                this.form.name = this.customer.name;
                this.form.type = this.customer.type;
                this.form.receiver_id = this.customer.receiver_id;
			}
        },
        CancelAddBranch() {
            this.showDialog = false;
        },
        SaveBranch() {
            this.form.clearErrors();
            this.form.put(
                route("customers.update", { customer: this.customer.Id }),
                {
                    preserveState: false,
                    onSuccess: () => {
                        this.showDialog = false;
                        this.$store.dispatch("setSuccessFlashMessage", true);
                    },
                }
            );
        },
        SaveNewBranch() {
            this.form.clearErrors();
            this.form.post(route("customers.store"), {
                preserveState: false,
                onSuccess: () => {
                    this.showDialog = false;
                    this.$nextTick(() => this.$emit("dataUpdated"));
                    this.$store.dispatch("setSuccessFlashMessage", true);
                },
            });
        },
        submit() {
            if (this.customer == null) this.SaveNewBranch();
            else this.SaveBranch();
        },
    },
};
</script>
