<template>
    <jet-dialog-modal :show="showDlg" max-width="xl" @close="showDlg = false">
        <template #title>
            {{ __("Invoice Review") }}
        </template>

        <template #content>
            <div>
                <jet-label :value="__('Receiving Branch')" />
                <multiselect
                    v-model="form.issuer"
                    label="name"
                    :options="branches"
                    placeholder="Select branch"
                />
            </div>
        </template>
        <template #footer>
            <div class="flex items-center justify-between mt-4">
                <jet-secondary-button @click="CancelDlg()">
                    {{__('Close')}}
                </jet-secondary-button>
                <div>
                    <jet-button class="ms-2" @click="UpdateItem()">
                        {{ __("Save") }}
                    </jet-button>
                </div>
            </div>
        </template>
    </jet-dialog-modal>
</template>

<script>
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetActionSection from "@/Jetstream/ActionSection";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDialogModal from "@/Jetstream/DialogModal";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetCheckbox from "@/Jetstream/Checkbox";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import JetValidationErrors from "@/Jetstream/ValidationErrors";
import TextField from "@/UI/TextField";
import Multiselect from "@suadelabs/vue3-multiselect";
import axios from "axios";
import swal from "sweetalert";
export default {
    components: {
        TextField,
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
    },

    props: ["modelValue"],
    data() {
        return {
            branches: [],
            item: this.modelValue,
            showDlg: false,
            form: this.$inertia.form({
                id: -1,
                issuer: "",
                comment: ""
            }),
        };
    },

    methods: {
        ShowDialog() {
            this.item = JSON.parse(JSON.stringify(this.modelValue));
            this.showDlg = true;
        },
        CancelDlg() {
            this.showDlg = false;
        },
		UpdateItem() {
            this.form.id = this.item.Id;
            axios
                .post(route("eta.invoices.received.update_details"), this.form)
                .then((response) => {
                    alert(response.data);
                })
                .catch((error) => {
                    alert(error.response.data);
                    //this.$refs.password.focus()
                });
        },
    },
    created: function created() {
        axios
            .get(route("json.branches"))
            .then((response) => {
                this.branches = response.data;
                if (this.item)
                    this.form.issuer = this.branches.find(
                        (option) => option.Id === this.item.issuer_id
                    );
            })
            .catch((error) => {});
    },
};
</script>
