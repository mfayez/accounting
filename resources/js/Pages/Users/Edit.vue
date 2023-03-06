<template>
    <jet-dialog-modal
        :show="showDialog"
        max-width="lg"
        @close="showDialog = false"
    >
        <template #title>
            {{ __("User Information") }}
        </template>

        <template #content>
            <jet-validation-errors class="mb-4" />

            <form v-if="$page.props.auth.user.is_admin" @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <jet-label for="name" :value="__('Name')" />
                        <jet-input
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                        />
                    </div>
                    <div>
                        <jet-label for="Email" :value="__('Email')" />
                        <jet-input
                            id="Email"
                            type="Email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                        />
                    </div>
                    <div>
                        <jet-label for="password" :value="__('Password')" />
                        <jet-input
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                        />
                    </div>
                    <div>
                        <jet-label :value="__('Role')" />
                        <select
                            id="type"
                            v-model="form.current_team_id"
                            class="mt-1 block w-full"
                        >
                            <option value="1">{{ __("Administrator") }}</option>
                            <option value="2">{{ __("Reviewer") }}</option>
                            <option value="3">{{ __("Data Entry") }}</option>
                            <option value="4">{{ __("ETA") }}</option>
                            <option value="5">{{ __("Viewer") }}</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <jet-label :value="__('Branches')" />
                        <multiselect
                            v-model="issuers"
                            :options="branches"
                            label="name"
                            :placeholder="__('Select Branches')"
                            :multiple="true"
                        />
                    </div>
                </div>
            </form>
            <div v-else>
                {{__("You are not authorized to add/edit users")}}
            </div>
        </template>
        <template #footer>
            <div class="flex items-center justify-end mt-4">
                <jet-secondary-button @click="CancelAddUser()">
                    {{ __("Cancel") }}
                </jet-secondary-button>

                <jet-button
                    class="ms-2"
                    @click="submit"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    v-if="$page.props.auth.user.is_admin"
                >
                    {{ __("Save") }}
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
import Multiselect from "@suadelabs/vue3-multiselect";
import axios from 'axios';

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
        pUser: {
            Type: Object,
            default: null,
        },
    },

    data() {
        return {
            branches: [],
            customers: [],
            issuers: [],
            errors: [],
            form: this.$inertia.form({
                name: "",
                email: "",
                password: "",
                issuers: [],
            }),
            showDialog: false,
        };
    },

    methods: {
        ShowDialog() {
            this.issuers = [];
            if (this.pUser !== null) {
                this.form.name = this.pUser.name;
                this.form.email = this.pUser.email;
                this.form.current_team_id = this.pUser.current_team_id;
                for (var i = 0; i < this.pUser.issuers.length; i++)
                    this.issuers.push(
                        this.branches.find(
                            (option) => option.Id === this.pUser.issuers[i].Id
                        )
                    );
            }
            this.showDialog = true;
        },
        CancelAddUser() {
            this.showDialog = false;
        },
        SaveUser() {
            this.form.issuers = [];
            for (var i = 0; i < this.issuers.length; i++)
                this.form.issuers.push(this.issuers[i].Id);
            axios
                .put(route("users.update", { user: this.pUser.id }), this.form)
                .then((response) => {
                    location.reload();
                })
                .catch((error) => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors; //.password[0];
                    //this.$refs.password.focus()
                });
        },
        SaveNewUser() {
            this.form.issuers = [];
            for (var i = 0; i < this.issuers.length; i++)
                this.form.issuers.push(this.issuers[i].Id);
            axios
                .post(route("users.store"), this.form)
                .then((response) => {
                    this.processing = false;
                    this.$nextTick(() => this.$emit("dataUpdated"));
                    this.form.reset();
                    this.form.processing = false;
                    this.showDialog = false;
                    location.reload();
                })
                .catch((error) => {
                    this.form.processing = false;
                    this.$page.props.errors = error.response.data.errors;
                    this.errors = error.response.data.errors; //.password[0];
                    //this.$refs.password.focus()
                });
        },
        submit() {
            if (this.pUser == null) this.SaveNewUser();
            else this.SaveUser();
        },
    },
    created: function created() {
        axios
            .get(route("json.branches"))
            .then((response) => {
                this.branches = response.data;
                if (this.pUser)
                    for (var i = 0; i < this.pUser.issuers.length; i++)
                        this.issuers.push(
                            this.branches.find(
                                (option) =>
                                    option.Id === this.pUser.issuers[i].Id
                            )
                        );
            })
            .catch((error) => {});
    },
};
</script>
