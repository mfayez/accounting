<template>
    <jet-dialog-modal :show="uploadingInvoices" @close="uploadingInvoices = false">
        <template #title>
            {{__('Uploading invoices from MOBIS')}}
        </template>

        <template #content>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <jet-label :value="__('Branch')" />
                    <multiselect
                        v-model="issuer"
                        label="name"
                        :options="branches"
                        placeholder="Select branch"
                    />
                </div>
                <div>
                    <jet-label :value="__('Branch Activity')" />
                    <multiselect
                        v-model="taxpayerActivityCode"
                        label="Desc_ar"
                        :options="activities"
                        placeholder="Select activity"
                    />
                </div>
                <div>
					<label>{{__('Choose Summary File')}}
						<input type="file" @change="handleFileUpload($event, 0)" ref="inputFile1"/>
					</label>
                </div>
                <div>
					<label>{{__('Choose Details File')}}
						<input type="file" @change="handleFileUpload($event, 1)" ref="inputFile2"/>
					</label>
                </div>
            </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="CancleDlg()">
                    {{__('Cancel')}}
                </jet-secondary-button>

                <jet-button class="ms-2" @click="submitFile()" :disabled="processing">
                    {{__('Save')}}
                </jet-button>
            </template>
        </jet-dialog-modal>
</template>

<style src="@suadelabs/vue3-multiselect/dist/vue3-multiselect.css"></style>

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
            Multiselect,
        },

        data() {
            return {
				file: ['',''],
				uploadingInvoices: false,
				processing: false,
                data: [],
                branches: [],
                issuer: [],
                taxpayerActivityCode: [],
                activities: [],
            }
        },

        methods: {
			ShowDialog() {
				this.uploadingInvoices = true;
                axios.get(route("json.branches"))
                    .then((response) => {
                        this.branches = response.data;
                        this.form.issuer = this.branches[0];
                    })
                    .catch((error) => {});
                axios.get("/json/ActivityCodes.json")
                    .then((response) => {
                        this.activities = response.data;
                        this.form.taxpayerActivityCode = this.activities[0];
                    })
                    .catch((error) => {});
			},
			CancleDlg() {
				this.uploadingInvoices = false;
			},
			handleFileUpload(event, idx){
  				this.file[idx] = event.target.files[0];
  			},
			submitFile() {
				let formData = new FormData();
				formData.append('file1', this.file[0]);
                formData.append('file2', this.file[1]);
                formData.append('issuer', this.issuer.Id);
                formData.append('taxpayerActivityCode', this.taxpayerActivityCode.code);
				this.processing = true;
				let temp = this;

				axios.post(route('ms.invoices.upload'),
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