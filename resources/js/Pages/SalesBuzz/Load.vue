<template>
    <jet-dialog-modal :show="showDlg" @close="showDlg = false">
		<template #title>
        	{{__('Loading invoices from Sales Buzz')}}
        </template>

        <template #content>
			<jet-validation-errors class="mb-4" />
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <jet-label
                        for="username"
                        :value="__('Username')"
                    />
                    <jet-input
                        id="username"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.username"
                        required
                        autofocus
                    />
                </div>
                <div>
                    <jet-label
                        for="password"
                        :value="__('Password')"
                    />
                    <jet-input
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                    />
                </div>
                <div>
                    <jet-checkbox name="settled_transactions" id="settled_transactions" v-model:checked="form.settled_transactions" />
					<jet-label for="settled_transactions" :value="__('Setteled Transactions')" class="ms-2"/>
                </div>
                <div>
                    <jet-checkbox name="tax_inverse" id="tax_inverse" v-model:checked="form.tax_inverse" />
					<jet-label for="tax_inverse" :value="__('Calcualte price based on tax')" class="ms-2"/>
                </div>
                <div class="lg:col-span-2">
                    <jet-label :value="__('Branch')" />
                    <multiselect
                        v-model="form.issuer"
                        label="name"
                        :options="branches"
                        placeholder="Select branch"
                    />
                </div>
                <div class="lg:col-span-2">
                    <jet-label :value="__('Branch Activity')" />
                    <multiselect
                        v-model="form.taxpayerActivityCode"
                        label="Desc_ar"
                        :options="activities"
                        placeholder="Select activity"
                    />
                </div>
                <div class="lg:col-span-2 flex justify-center">
                    <jet-label :value="lastDate" />
                </div>
                <div class="lg:col-span-2 flex justify-center">
                    <jet-label :value="lastInv" />
                </div>
            </div>
			<div class="mt-8">
				<label for="sync1">{{__('Synchronizing Invoices...')}}</label><br/>
				<progress class="w-full" id="sync1" :value="progress1.value" :max="progress1.maxValue"> 
					{{progress1.value}}% 
				</progress>
			</div>
		</template>
		<template #footer>
			<div class="flex items-center justify-end mt-4">
	    		<jet-secondary-button @click="CancelImport()">
   					{{__('Cancel')}}
        		</jet-secondary-button>
                <jet-button
                    class="ms-2"
                    @click="LoadSB"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                >
                    {{ __("Start") }}
                </jet-button>
			</div>
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
    import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
    import swal from "sweetalert";
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
			JetValidationErrors,
            Multiselect,
        },

        data() {
            return {
				errors: [],
                activities: [],
                branches: [],
				progress1: {
					value: 0,
					maxValue: 100
				},
                lastDate: 'N/A',
                lastInv: 'N/A',
                processing: false,
				form: {
					value: 0,
                    username: "",
                    password: "",
                    buid: "",
                    taxpayerActivityCode: "",
                    period: 7,
                    issuer: "",
                    settled_transactions: false,
                    tax_inverse: false,
				},
				showDlg: false,
            }
        },

        methods: {
			ShowDialog() {
				this.showDlg = true;
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
			CancelImport() {
				this.showDlg = false;
			},
			LoadSB() {
                if (this.showDlg == false) {
                    this.form.value = 0;
                    this.progress1.value = 0;
                    this.progress1.maxValue = 100;
                    this.form.username = "";
                    this.form.password = "";
                    this.form.buid = "";
                    this.form.issuer = "";
                    return;
                }
                this.form.value = this.progress1.value + 1;                
                axios.post(route('sb.sync_orders'), this.form)
                    .then(response => {
                        if (response.data.code == 404){
                            swal("Error", response.data.message, "error");
                            return;
                        }
                        this.progress1.maxValue = response.data.totalPages;
                        this.lastDate = response.data.lastDate;
                        this.lastInv = response.data.lastInvoice;
                        this.progress1.value  = this.progress1.value + 1;
                        if (this.progress1.value < this.progress1.maxValue)
                            this.$nextTick(() => this.LoadSB());
                    }).catch(error => {
                        this.$page.props.errors = error.response.data.errors;
                        this.errors = error.response.data.errors;
                         swal({
                            title: __("Error in loading invoices"),
                            icon: "success",
                        });
                    });
			}
        },
    }
</script>

