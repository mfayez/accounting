<template>
    <!-- prettier-ignore -->
    <div class="py-8">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="card-title flex flex-col lg:flex-row justify-between p-3">
                    <h4 class="capitalize">
                        {{ __('Archives') }}
                    </h4>
                </div>
                <hr />
                <div class="result my-5 overflow-x-auto w-full" v-if="data.length > 0">
                    <table class="w-11/12 mx-auto max-w-4xl lg:max-w-full">
                        <thead class="text-center bg-gray-300">
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('From') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('To') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Status') }}
                            </th>
                            <th 
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{__('Actions')}}
                            </th>
                        </thead>
                        <tbody class="text-center border border-[#eceeef]">
                            <tr
                                class="border border-[#eceeef]"
                                v-for="row in data"
                                :key="row"
                            >
                                <td class="p-2 border border-[#eceeef]">
                                    {{ new Date(row.queryParams.dateFrom).toLocaleString() }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ new Date(row.queryParams.dateTo).toLocaleString() }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    <span
                                        class="text-white py-1 px-5 inline-block my-1 rounded-xl capitalize"
                                        :class="'status-' + String(row.status)"
                                    >
                                        {{ __(statusString(row.status)) }}</span
                                    >
                                </td>
                                <td>
                                    <jet-button class="ms-2" @click="LoadInvoices(row.packageId)">
                                        {{ __("Load to System") }}
                                    </jet-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else>
                    <p class="text-center text-red-600 my-5">
                        <i class="fa fa-exclamation-circle mr-1"></i>
                        {{ __('No Records Were Found') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetButton from "@/Jetstream/Button.vue";

export default {    
    components: {
        JetButton
    },
    props: {
        data: {
            required: true,
            type: Array,
        },
    },
    data() {
        return {
            syncResult: []
        };
    },
    methods: {
        statusString(status) {
            if (status == 1) return "Processing";
            if (status == 2) return "Ready";
            if (status == 3) return "Error";
            if (status == 4) return "Deleted";
            return "Unknow";
        },
        LoadInvoices(rid){
            window.open(route('archive.import', {rid: rid}), '_blank');
        }
    },
    mounted() {
        //
    },
};
</script>
<style scoped>
.status-1 {
    background-color: #0e9f6e;
}
.status-2 {
    background-color: #1abc9c;
}
.status-3 {
    background-color: #f05252;
}
.status-4 {
    background-color: #f05252;
}
</style>
