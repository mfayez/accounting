<template>
    <span>
        <span @click="saveItem">
            <slot />
        </span>

        <jet-dialog-modal :show="editingItem" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                <div class="mt-4">
                    <jet-input type="text" class="mt-1 block w-3/4" placeholder="Password"
                                ref="description"
                                v-model="item.description" />

                    <jet-input-error :message="error" class="mt-2" />
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="closeModal">
                    Cancel
                </jet-secondary-button>

                <jet-button class="ms-2" @click="confirmPassword" :class="{ 'opacity-25': processing }" :disabled="processing">
                    {{ button }}
                </jet-button>
            </template>
        </jet-dialog-modal>
    </span>
</template>

<script>
    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        emits: ['saved'],

        props: {
			item: null,
            title: {
                default: 'Editing Item',
            },
            button: {
                default: 'Save',
            }
        },

        components: {
            JetButton,
            JetDialogModal,
            JetInput,
            JetInputError,
            JetSecondaryButton,
        },

        data() {
            return {
                editingItem: false,
				processing: false,
				error: null,
            }
        },

        methods: {
            saveItem() {
                axios.get(route('password.confirmation')).then(response => {
                    if (response.data.confirmed) {
                        this.$emit('confirmed');
                    } else {
                        this.editingItem = true;

                        setTimeout(() => this.$refs.password.focus(), 250)
                    }
                })
            },

            confirmPassword() {
                this.processing = true;

                axios.post(route('password.confirm'), {
                    password: this.form.password,
                }).then(() => {
                    this.processing = false;
                    this.closeModal()
                    this.$nextTick(() => this.$emit('confirmed'));
                }).catch(error => {
                    this.processing = false;
                    this.error = error.response.data.errors.password[0];
                    this.$refs.password.focus()
                });
            },

            closeModal() {
                this.editingItem = false
            },
        }
    }
</script>
