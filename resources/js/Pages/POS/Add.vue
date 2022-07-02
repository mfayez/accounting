<template>
    <jet-dialog-modal :show="showDialog" @close="showDialog = false">
        <template #title>
            {{ __("Add New POS") }}
        </template>

        <template #content>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <TextField v-model="form.name" itemType="input" :itemLabel="__('POS Name')" />
                    </div>
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
                        <TextField v-model="form.serial" itemType="input" :itemLabel="__('Serial Number')" />
                    </div>
                    <div>
                        <TextField v-model="form.os_version" itemType="input" :itemLabel="__('Version')" />
                    </div>
                    <div>
                        <TextField v-model="form.model" itemType="input" :itemLabel="__('Model')" />
                    </div>
                    <div>
                        <TextField v-model="form.pos_key" itemType="input" :itemLabel="__('POS Key')" />
                    </div>
                    <div v-if="false">
                        <TextField v-model="form.grant_type" itemType="input" :itemLabel="__('Grant Type')" />
                    </div>
                    <div class="col-span-2">
                        <TextField v-model="form.client_id" itemType="input" :itemLabel="__('Client ID')" />
                    </div>
                    <div class="col-span-2">
                        <TextField v-model="form.client_secret" itemType="input" :itemLabel="__('Client Secret')" />
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
                    {{ __("Save") }}
                </jet-button>
            </div>
        </template>
    </jet-dialog-modal>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>

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
        Multiselect,
    },

    props: {
        positem: {
            Type: Object,
            default: null,
        },
    },

    data() {
        return {
            errors: [],
            branches: [],
            form: this.$inertia.form({
                name: "POS1",
                serial: "253142",
                os_version: "os",
                model: "Legacy_Model",
                pos_key: " ",
                grant_type: "client_credentials",
                client_id: "779bf63a-e6a3-452d-a7c9-09b80a726b14",
                client_secret: "17d01008-a5a6-45d2-b148-852bbd5a8f86",
                issuer_id: '',
                issuer: ''
            }),
            showDialog: false,
        };
    },

    methods: {
        ShowDialog() {
            if (this.positem !== null) {
                this.form.id            = this.positem.id;
                this.form.name          = this.positem.name;
                this.form.serial        = this.positem.serial;
                this.form.os_version    = this.positem.os_version;
                this.form.model         = this.positem.model;
                this.form.pos_key       = this.positem.pos_key;
                this.form.grant_type    = this.positem.grant_type;
                this.form.client_id     = this.positem.client_id;
                this.form.client_secret = this.positem.client_secret;
                this.form.issuer_id     = this.positem.issuer_id;
            }
            this.showDialog = true;
        },
        CancelAddRequest() {
            this.form.reset();
            this.showDialog = false;
        },
        submit() {
            this.form.issuer_id = this.form.issuer.Id;
            if ((this.positem !== null)){
                axios
                    .put(route("pos.update", this.positem.id), this.form)
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
            }
            else{
                axios
                    .post(route("pos.store"), this.form)
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
            }
            
        },
        
    },
    created: function created() {
        axios
            .get(route("json.branches"))
            .then((response) => {
                this.branches = response.data;
                if (this.positem)
                    this.form.issuer = this.branches.find(
                        (option) => option.Id === this.positem.issuer_id
                    );
            })
            .catch((error) => {});
    },
};
</script>
