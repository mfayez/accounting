<template>
        <!-- New Customer Modal -->
        <jet-dialog-modal :show="uploadingInvoices" @close="uploadingInvoices = false">
            <template #title>
                {{__('Upload Invoices')}}
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
                    <a href="/ExcelTemplates/InvoiceUpload.xlsx">{{__('Download excel template')}}</a>
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
				file: '',
				uploadingInvoices: false,
				processing: false,
                data: []
            }
        },

        methods: {
			ShowDialog() {
				this.uploadingInvoices = true;
			},
			CancelAddCustomer() {
				this.uploadingInvoices = false;
			},
			SaveNewCustomer() {
				this.uploadingInvoices = false;
			},
			handleFileUpload(event){
  				this.file = event.target.files[0];
  			},
			submitFile() {
				let formData = new FormData();
				formData.append('file', this.file);
				this.processing = true;
				let temp = this;

				axios.post(route('eta.invoices.upload'),
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
