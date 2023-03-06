<template>
	<div>
		<jet-label :for="itemId" :value="itemLabel" />
		<jet-input :inputId="itemId" :type="itemType" class="mt-1 block w-full h-9 disabled:bg-gray-400" 
            v-model="localValue" required autofocus 
			@update:modelValue="$emit('update:modelValue', $event)" :disabled="!active" 
		/>
	</div>
</template>

<script>
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetActionSection from '@/Jetstream/ActionSection.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetCheckbox from '@/Jetstream/Checkbox.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
    import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'

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
                localValue: {
                    type:[String, Number],
                    default: ''
                }
            }
        },

        methods: {
        },
    }
</script>

