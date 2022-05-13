<template>
    <jet-dialog-modal :show="showDialog" @close="showDialog = false">
        <template #title>
            {{ __("Request Archive Package") }}
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <TextField v-model="form.startDate" itemType="date" :itemLabel="__('Start Date')" />
                    </div>
                    <div>
                        <TextField v-model="form.endDate" itemType="date" :itemLabel="__('End Date')" />
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex items-center justify-end">
                <jet-secondary-button @click="CancelAddRequest()">
                    {{ __("Cancel") }}
                </jet-secondary-button>

                <jet-button
                    class="ms-2"
                    @click="submit"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ __("Send") }}
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
import TextField from '@/UI/TextField'

export default {
    components: {
        TextField,
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
        //todo
    },

    data() {
        return {
            errors: [],
            form: this.$inertia.form({
                startDate: new Date().toISOString().slice(0, 10),
				endDate: new Date().toISOString().slice(0, 10)
            }),
            showDialog: false,
        };
    },

    methods: {
        ShowDialog() {
            this.showDialog = true;
        },
        CancelAddRequest() {
            this.showDialog = false;
        },
        submit() {
            axios
                .post(route("archive.store"), this.form)
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.$nextTick(() => this.$emit("dataUpdated"));
                    this.form.reset();
                    this.form.processing = false;
                    this.showDialog = false;
                    //setTimeout(() => {
                    //    window.location.reload();
                    //}, 500);
                })
                .catch((error) => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                });
        },
        
    },
};
</script>
