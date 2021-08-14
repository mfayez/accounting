<template>
        <!-- New Customer Modal -->
        <jet-dialog-modal :show="addingNewCustomer" @close="addingNewCustomer = false">
            <template #title>
                Add new customer
            </template>

            <template #content>
                <div class="grid grid-cols-3 md:grid-cols-3 gap-4">
                	<div>
						<label class="flex items-center">Name:</label>
                    </div>
                	<div>
						<input type="text" :value="form.name"/>
               		</div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="CancelAddCustomer()">
                    Cancel
                </jet-secondary-button>

                <jet-button class="ml-2" @click="SaveNewCustomer()" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </jet-button>
            </template>
        </jet-dialog-modal>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetButton from '@/Jetstream/Button'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal'
    import JetDangerButton from '@/Jetstream/DangerButton'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetCheckbox from '@/Jetstream/Checkbox'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetSectionBorder from '@/Jetstream/SectionBorder'

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
        },

        props: [
            'customer',
        ],

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                }),
				addingNewCustomer: false,
            }
        },

        methods: {
			ShowDialog() {
				this.addingNewCustomer = true;
			},
			CancelAddCustomer() {
				this.addingNewCustomer = false;
			},
			SaveNewCustomer() {
				this.addingNewCustomer = false;
			},
        },
    }
</script>
