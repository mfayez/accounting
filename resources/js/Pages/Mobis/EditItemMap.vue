<template>
    <jet-dialog-modal :show="showDialog" maxWidth="3xl" @close="showDialog = false">
        <template #title>
            {{ __("Item Map") }}
        </template>

        <template #content>
            <jet-validation-errors class="mb-4" />

            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <jet-label :value="__('MSCode')" />
                        <jet-input
                            id="MSCode"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.MSCode"
                            :disabled="true"
                        />
                    </div>
                    <div class="col-span-2">
                        <jet-label :value="__('ETACode')" />
                        <multiselect
                            v-model="eta_item"
                            :options="items"
                            :custom-label="nameWithCode"
                            label="codeNamePrimaryLang"
                            track-by="itemCode"
                            @update:model-value="updateETAItem"
                            :placeholder="__('Select item')"
                        />
                    </div>
                    <div class="col-span-1">
                        <jet-label :value="__('Name Arabic')" />
                        <jet-input
                            id="MSCode"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.ItemNameA"
                        />
                    </div>
                    <div class="col-span-1">
                        <jet-label :value="__('Name English')" />
                        <jet-input
                            id="MSCode"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.ItemNameE"
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
        item_map: {
            Type: Object,
            default: null,
        },
    },

    data() {
        return {
            errors: [],
            form: this.$inertia.form({
                MSCode: "",
                ETACode: "",
                ItemNameA: "",
				ItemNameE: "",
                Val_Diff: 0,
            }),
            eta_item: null,
            items: [],
            showDialog: false,
        };
    },

    methods: {
        ShowDialog() {
            if (this.item_map !== null) {
                this.form.MSCode = this.item_map.MSCode;
                this.form.ETACode = this.item_map.ETACode;
                this.form.ItemNameA = this.item_map.ItemNameA;
                this.form.ItemNameE = this.item_map.ItemNameE;
                this.form.Val_Diff = this.item_map.Val_Diff;
            }
            this.showDialog = true;
            this.$nextTick(() => {
                if (this.item_map != null && this.items != null) {
                    this.eta_item = this.items.find(
                        (option) => option.itemCode === this.form.ETACode
                    );
                    this.updateETAItem();
                }
            });
        },
        CancelDlg() {
            this.showDialog = false;
        },
        submit() {
            axios
                .post(route("ms.items.map.update"), this.form)
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
                    this.errors = error.response.data.errors; //.password[0];
                    //this.$refs.password.focus()
                });
        },
        nameWithCode ({ codeNamePrimaryLang, codeNameSecondaryLang, itemCode }) {
            if (this.$page.props.locale == 'ar')
                return itemCode + ' - ' + codeNameSecondaryLang;
            else
                return itemCode + ' - ' +  codeNamePrimaryLang;
        },
        updateETAItem () {
            if (this.eta_item){
                if (this.form.ItemNameA == null || this.form.ItemNameA.length < 3) 
                    this.form.ItemNameA = this.eta_item.codeNameSecondaryLang;
                if (this.form.ItemNameE == null || this.form.ItemNameE.length < 3)
                    this.form.ItemNameE = this.eta_item.codeNamePrimaryLang;
                this.form.ETACode = this.eta_item.itemCode;
            }
        },
    },
    created() {
        axios
            .get(route("json.eta.items"))
            .then((response) => {
                this.items = response.data;
                if (this.item_map != null) {
                    this.eta_item = this.items.find(
                        (option) => option.itemCode === this.form.ETACode
                    );
                    this.updateETAItem();
                }
            })
            .catch((error) => {});
    },
};
</script>
