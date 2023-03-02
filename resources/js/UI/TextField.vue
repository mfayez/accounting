<template>
	<div>
		<jet-label :for="itemId" :value="itemLabel" />
		<jet-input :inputId="itemId" :type="itemType" class="mt-1 block w-full h-9 disabled:bg-gray-400" v-model="modelValue" required autofocus 
			@update:modelValue="$emit('update:modelValue', $event)" :disabled="!active" 
		/>
	</div>
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
    import JetValidationErrors from '@/Jetstream/ValidationErrors'

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
        },
        props: {
			itemLabel: String, 
			itemId: String,
			itemType: String,
            //define modelValue to have string or number type
            modelValue: {
                type: [String, Number],
                default: ''
            },
            active: {
				Type: Boolean,
				default: true
			}
        },

        emits: ['update:modelValue'],

        data() {
            return {
            }
        },

        methods: {
        },
    }
</script>

