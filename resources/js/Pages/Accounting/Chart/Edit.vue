<template>
    <jet-dialog-modal :show="showDialog" maxWidth="3xl" @close="showDialog = false">
        <template #title>
            {{ __("Accounting Chart Dialog") }}
        </template>

        <template #content>
            <jet-validation-errors class="mb-4" />

            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <jet-label :value="__('Code')" />
                        <jet-input
                            id="id"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.id"
                            :disabled="true"
                        />
                    </div>
					<div class="col-span-2">
                        <jet-label :value="__('Parent')" />
                        <multiselect
                            v-model="parent_item"
                            :options="items"
                            :custom-label="nameWithCode"
                            label="name"
                            track-by="id"
                            @update:model-value="updateParentItem"
                            :placeholder="__('Select item')"
                        />
                    </div>
                    <div class="col-span-2">
                        <jet-label :value="__('Name')" />
                        <jet-input
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                        />
                    </div>
                    <div class="col-span-2">
                        <jet-label :value="__('Description')" />
                        <jet-input
                            id="description"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.description"
                        />
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <div class="flex items-center justify-end">
                <jet-secondary-button @click="CancelDlg()">
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
import Multiselect from "@suadelabs/vue3-multiselect";

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
        Multiselect,
    },

    props: {
        accounting_item: {
            Type: Object,
            default: null,
        },
    },

    data() {
        return {
            errors: [],
            form: this.$inertia.form({
                id: "",
                parent_id: "",
                name: "",
				description: "",
                status: ""
            }),
            parent_item: null,
            items: [],
            showDialog: false,
        };
    },

    methods: {
        ShowDialog() {
            if (this.accounting_item !== null) {
                this.form.id = this.accounting_item.id;
                this.form.parent_id = this.accounting_item.parent_id;
                this.form.name = this.accounting_item.name;
                this.form.description = this.accounting_item.description;
                this.form.status = this.accounting_item.status;
            }
            this.showDialog = true;
            this.$nextTick(() => {
                if (this.accounting_item != null && this.items != null) {
                    this.parent_item = this.items.find(
                        (option) => option.itemCode === this.form.ETACode
                    );
                    this.updateParentItem();
                }
            });
        },
        CancelDlg() {
            this.showDialog = false;
        },
        submit() {
            axios
                .post(route("accounting.chart.store"), this.form)
                .then((response) => {
                    this.$store.dispatch("setSuccessFlashMessage", true);
                    this.processing = false;
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
                    this.errors = error.response.data.errors;
                    
                });
        },
        nameWithCode ({ id, name }) {
            return id + ' - ' + name;
        },
        updateParentItem () {
            if (this.parent_item){
                this.form.parent_id = this.parent_item.id;
            }
        },
    },
    created() {
        axios
            .get(route("accounting.chart.json"))
            .then((response) => {
                this.items = response.data;
                if (this.accounting_item != null) {
                    this.parent_item = this.items.find(
                        (option) => option.id === this.form.parent_id
                    );
                    this.updateParentItem();
                }
            })
            .catch((error) => {});
    },
};
</script>
