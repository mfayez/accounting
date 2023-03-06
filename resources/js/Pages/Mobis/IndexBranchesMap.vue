<template>
    <!-- prettier-ignore -->
    <app-layout>
		<div class="py-4">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <table class="w-11/12 mx-auto max-w-4xl lg:max-w-full">
                        <thead class="text-center bg-gray-300">
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef] w-2/6">{{__('Branch Name')}}</th>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef] w-2/6">{{__('MOBIS URL')}}</th>
                            <th class="bg-[#f8f9fa] p-3 border border-[#eceeef] w-1/6">{{__('Actions')}}</th>
						</thead>
						<tbody>
					  		<tr v-for="item in items" :key="item.BID">
                                <td>
                                    {{item.BName}}
                                </td>
                                <td>
                                    <jet-input
                                        id="MSUrl"
                                        type="test"
                                        class="mt-1 block w-full"
                                        v-model="item.SBUrl"
                                    />
                                </td>
                                <td>
                                    <jet-button
                                        class="me-2 mt-2"
                                        @click="saveItem(item)" 
                                    >
                                        {{ __("Save Edit") }}
                                    </jet-button>
                                </td>
							</tr>
						</tbody>
				 	</Table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import Confirm from "@/UI/Confirm.vue";
import JetLabel from "@/Jetstream/Label.vue";
import SecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from '@/Jetstream/DangerButton.vue';
import Dropdown from "@/Jetstream/Dropdown.vue";
import JetInput from "@/Jetstream/Input.vue";
import swal from "sweetalert";

export default {
    components: {
        Dropdown,
        JetInput,
        AppLayout,
        Confirm,
        JetLabel,
        JetButton,
        JetDangerButton,
        SecondaryButton,
    },
    props: {
        items: Object,
    },
    data() {
        return {
        };
    },
    methods: {
        saveItem: function (item) {
            axios
                .post(route("ms.branches.map.update"), item)
                    .then((response) => {
                        this.$store.dispatch("setSuccessFlashMessage", true);
                        setTimeout(() => {
                            window.location.reload();
                        }, 100);
                    })
                    .catch((error) => {
                        console.log(error);
                        this.form.processing = false;
                        this.$page.props.errors = error.response.data.errors;
                        this.errors = error.response.data.errors; //.password[0];
                        
                    });
        },
    },
};
</script>
<style scoped>
:deep(table td) {
    text-align: center;
	white-space: pre-line;
}
:deep(table th) {
    text-align: center;
	white-space: pre-line;
}
</style>
