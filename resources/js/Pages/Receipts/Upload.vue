<template>
        <!-- New Customer Modal -->
        <jet-dialog-modal :show="uploadingReceipts" @close="uploadingReceipts = false">
            <template #title>
                {{__('Upload Receipts')}}
            </template>

            <template #content>
                <div class="grid grid-cols-3 md:grid-cols-3 gap-4">
					<label>{{__('Choose File')}}
						<input type="file" @change="handleFileUpload($event)" ref="inputFile"/>
					</label>
                </div>
                <template v-for="row in data">
                    <div v-if="row.hasError">
                        {{row.error}}
                    </div>
                </template>
                <div class="mt-4 flex justify-end">
                    <a href="/ExcelTemplates/ReceiptUpload.xlsx">{{__('Download excel template')}}</a>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="CancelAddCustomer()">
                    {{__('Cancel')}}
                </jet-secondary-button>

                <jet-button class="ms-2" @click="submitFile()" :disabled="processing">
                    {{__('Save')}}
                </jet-button>
            </template>
        </jet-dialog-modal>
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
				file: '',
				uploadingReceipts: false,
				processing: false,
                data: []
            }
        },

        methods: {
			ShowDialog() {
				this.uploadingReceipts = true;
			},
			CancelAddCustomer() {
				this.uploadingReceipts = false;
			},
			SaveNewCustomer() {
				this.uploadingReceipts = false;
			},
			handleFileUpload(event){
  				this.file = event.target.files[0];
  			},
			submitFile() {
				let formData = new FormData();
				formData.append('file', this.file);
				this.processing = true;
				let temp = this;

				axios.post(route('eta.receipts.upload'),
				  formData,
				  {
					headers: {
						'Content-Type': 'multipart/form-data'
					}
				  }
				).then((response)=>{
					temp.processing = false;
					temp.$refs.inputFile.value = null;
                    this.data = response.data;
					//temp.closeModal()
				})
				.catch(function(){
					temp.processing = false;
					temp.$refs.inputFile.value = null;
				    console.log('FAILURE!!');
				});
			}
			
        },
    }
</script>
