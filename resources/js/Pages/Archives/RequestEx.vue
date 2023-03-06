<template>
    <jet-dialog-modal :show="showDialog" @close="showDialog = false">
        <template #title>
            {{ __("Prepare Archive") }}
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <jet-label :value="__('Branch')" />
                        <multiselect
                            v-model="form.issuer"
                            label="name"
                            :options="branches"
                            placeholder="Select branch"
                        />
                    </div>
                    <div>
                        <jet-label :value="__('Customer')" />
                        <multiselect
                            v-model="form.receiver"
                            label="name"
                            :options="customers"
                            placeholder="Select customer"
                        />
                    </div>
                    <div>
                        <TextField v-model="form.start_date" itemType="date" :itemLabel="__('Start Date')" />
                    </div>
                    <div>
                        <TextField v-model="form.end_date" itemType="date" :itemLabel="__('End Date')" />
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

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>

<script>
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
import TextField from '@/UI/TextField.vue'
import Multiselect from "@suadelabs/vue3-multiselect";
import axios from 'axios';

export default {
    components: {
        Multiselect,
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
            branches: [],
            customers: [],
            form: this.$inertia.form({
                issuer: "",
                receiver: "",
                start_date: new Date().toISOString().slice(0, 10),
				end_date: new Date().toISOString().slice(0, 10)
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
                .post(route("archive.request.store"), this.form)
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.$nextTick(() => this.$emit("dataUpdated"));
                    this.form.reset();
                    this.form.processing = false;
                    this.showDialog = false;
                })
                .catch((error) => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors;
                });
        },
    },
    created: function created() {
        axios
            .get(route("json.branches"))
            .then((response) => {
                var temp = [{ Id: -1, name: "All" }];
                this.branches = temp.concat(response.data);
            })
            .catch((error) => {
                console.log(error);
            });
        axios
            .get(route("json.customers"))
            .then((response) => {
                var temp = [{ Id: -1, name: "All" }];
                this.customers = temp.concat(response.data);
            })
            .catch((error) => {
                console.log(error);
            });
    },
};
</script>
