<template>
        <jet-dialog-modal :show="showDialog" @close="closeModal" max-width="xl">
            <template #title>
                {{ __(title) }}
            </template>

            <template #content>
				<slot></slot>
            </template>

            <template #footer>
                <jet-secondary-button @click="closeModal">
                    {{__('No')}}
                </jet-secondary-button>

                <jet-button class="ms-2" @click="confirm">
                    {{__('Yes')}}
                </jet-button>
            </template>
        </jet-dialog-modal>
</template>

<script>
    import JetButton from '@/Jetstream/Button'
    import JetDialogModal from '@/Jetstream/DialogModal'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        emits: ['confirmed'],

        props: {
			message: {
				default: 'Are you sure?'
			},
			item: null,
            title: {
                default: 'Confirmation',
            },
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
				showDialog: false
            }
        },

        methods: {
			ShowModal() {
				this.showDialog = true;
			},
            confirm() {
                this.$nextTick(() => this.$emit('confirmed'));
				this.showDialog = false;
            },
            closeModal() {
                this.showDialog = false
            },
        }
    }
</script>
