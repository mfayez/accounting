<template>
    <!-- prettier-ignore -->
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="card-title flex flex-col lg:flex-row justify-between p-3">
                    <h4 class="capitalize">
                        {{ title }}
                    </h4>
                    <p class="mt-3 lg:mt-0">
                        <i class="fas fa-calendar"></i> {{ __('From') }} {{ from }} {{ __('To') }}
                        {{ to }}
                    </p>
                </div>
                <hr />
                <div class="result my-5 overflow-x-auto w-full" v-if="data.length > 0">
                    <table class="w-11/12 mx-auto max-w-4xl lg:max-w-full">
                        <thead class="text-center bg-gray-300">
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Invoices') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef] hidden md:table-cell"
                            >
                                {{ __('Net Amount') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Gross Amount') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef] hidden md:table-cell"
                            >
                                {{ __('Tax Amount') }}
                            </th>
                            <th
                                class="bg-[#f8f9fa] p-3 border border-[#eceeef]"
                            >
                                {{ __('Status') }}
                            </th>
                        </thead>
                        <tbody class="text-center border border-[#eceeef]">
                            <tr
                                class="border border-[#eceeef]"
                                v-for="row in data"
                                :key="row"
                            >
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.invoicesCount }}
                                </td>
                                <td class="p-2 border border-[#eceeef] hidden md:table-cell">
                                    {{ row.totalSalesAmount }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    {{ row.totalAmount }}
                                </td>
                                <td class="p-2 border border-[#eceeef] hidden md:table-cell">
                                    {{ row.taxTotal }}
                                </td>
                                <td class="p-2 border border-[#eceeef]">
                                    <span
                                        class="text-white py-1 px-5 inline-block my-1 rounded-xl capitalize"
                                        :class="String(row.Status).replace(' ' , '-').toLowerCase()"
                                    >
                                        {{ __(row.Status) }}</span
                                    >
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

<script setup>
import { defineProps } from "vue";

const props = defineProps({
    title: {
        required: true,
        type: String,
    },
    from: {
        required: true,
        type: String,
    },
    to: {
        required: true,
        type: String,
    },
    data: {
        required: true,
        type: Array,
    },
});
</script>
<style scoped>
.valid {
    background-color: #0e9f6e;
}
.submitted {
    background-color: #1abc9c;
}
.processing {
    background-color: #1abc9c;
}
.review {
    background-color: #0e6e9f;
}
.cancelled {
    background-color: #000;
}
.invalid {
    background-color: #f05252;
}
.rejected {
    background-color: #f05252;
}
.pending {
    background-color: #34495e;
}
.in-review {
    background-color: #f1c40f;
}
.approved {
    background-color: #27ae60;
}
</style>
